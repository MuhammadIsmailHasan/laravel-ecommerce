<?php

namespace App\Http\Controllers\Ecommerce;

use Illuminate\Support\Str;
use App\City;
use App\Customer;
use App\District;
use App\Http\Controllers\Controller;
use App\Mail\CustomerRegisterMail;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function Ramsey\Uuid\v1;

class CartController extends Controller
{
    protected $path = 'ecommerce';

    public function getCard()
    {
        //COOKIE BENTUKNYA JSON MAKA KITA GUNAKAN JSON_DECODE UNTUK MENGUBAHNYA MENJADI ARRAY
        //$carts = json_decode($request->cookie('ec-cards'), true); //JIKA PAKAI tipe $REQUEST BEGINI, AKAN ERROR
        $cards = json_decode(request()->cookie('ec-cards'), true);
        $cards = $cards != null ? $cards:[];

        return $cards;
    }
    
    public function getSubtotal($carts)
    {
        $subtotal = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['product_price'];
        });

        return $subtotal;
    }

    public function getCity(Request $request)
    {
        $cities = City::where('province_id', $request->province_id)
                ->orderBy('name', 'ASC')->get();    
        return response()->json([
            'status' => 'success',
            'data' => $cities
        ]);
    }

    public function getDistrict()
    {
        $districts = District::where('city_id', request()->city_id)
                ->orderBy('name', 'ASC')->get();

        return response()->json([
            'status' => 'success',
            'data' => $districts
        ]);
    }

    public function addToCart(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer'
        ]);
        
        $carts = $this->getCard();
        //CEK JIKA CARTS TIDAK NULL DAN PRODUCT_ID ADA DIDALAM ARRAY CARTS
        if ($carts && array_key_exists($request->product_id, $carts)) {
            $carts[$request->product_id]['qty'] += $request->qty;
        } else {
            $product = Product::findOrFail($request->product_id);

            $carts[$request->product_id] = [
                'qty' => $request->qty,
                'product_id' => $product->id, 
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->image
            ];
        }

        //BUAT COOKIE-NYA DENGAN NAME EC-CARTS
        //DI-ENCODE KEMBALI, DAN LIMITNYA 2800 MENIT ATAU 48 JAM
        $cookie = cookie('ec-cards', json_encode($carts, 2800));

        return redirect()->back()->cookie($cookie);
    }

    public function listCart()
    {
        $carts = $this->getCard();
        
        $subtotal = $this->getSubtotal($carts);

        return view($this->path . '.cart', compact('carts', 'subtotal'));
    }
    
    public function updateCart(Request $request)
    {
        $carts = $this->getCard();

        //KEMUDIAN LOOPING DATA PRODUCT_ID, KARENA NAMENYA ARRAY PADA VIEW SEBELUMNYA
        //MAKA DATA YANG DITERIMA ADALAH ARRAY SEHINGGA BISA DI-LOOPING
        foreach ($request->product_id as $key => $row) {
            if ($request->qty[$key] == 0) {
                unset($carts[$row]);
            } else {
                $carts[$row]['qty'] = $request->qty[$key];
            }
        }

        $cookie = cookie('ec-cards', json_encode($carts), 2800);

        return redirect()->back()->cookie($cookie);
    }

    public function checkout()
    {
        $auth = auth()->guard('customer')->user();

        $provinces = Province::orderBy('name', 'ASC')->get();
        $carts = $this->getCard();
        $subtotal = $this->getSubtotal($carts);

        return view($this->path . '.checkout', compact('auth', 'provinces', 'carts', 'subtotal'));
    }

    public function processCheckout(Request $request)
    {

        $this->validate($request, [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required',
            'email' => 'required|email',
            'customer_address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
        ]);
        
        DB::beginTransaction();
        try {
            $customer = Customer::where('email', $request->email)->first();

            //JIKA DIA TIDAK LOGIN DAN DATA CUSTOMERNYA ADA
            if (!auth()->guard('customer')->check() && $customer) {
                return redirect()->back()->with(['error' => 'Silahkan Login Terlebih Dahulu']);
            }

            $carts = $this->getCard();
            $subtotal = $this->getSubtotal($carts);
            $password = Str::random(8);
            $activeToken = Str::random(30);

            if (!auth()->guard('customer')->check()) {
                $customer = Customer::create([
                    'district_id' => $request->district_id,
                    'name' => $request->customer_name,
                    'email' => $request->email,
                    'password' => $password,
                    'phone' => $request->customer_phone,
                    'address' => $request->customer_address,
                    'activate_token' => $activeToken,
                    'status' => false
                ]);
            }

            $order = Order::create([
                'invoice' => Str::random(4) . '-' . time(),
                'district_id' => $request->district_id,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'subtotal' => $subtotal
            ]);

            foreach ($carts as $key => $row) {
                $product = Product::findOrFail($row['product_id']);

                OrderDetail::create([
                    'order_id' => $order->getKey(),
                    'product_id' => $row['product_id'],
                    'price' => $row['product_price'],
                    'qty' => $row['qty'],
                    'weight' => $product->weight
                ]);
            }

            DB::commit();
            
            $carts = [];
            $cookie = cookie('ec-carts', json_encode($carts, 2800));

            if (!auth()->guard('customer')->check()) {
                Mail::to($request->email)->send(new CustomerRegisterMail($customer, $password));
            }

            return redirect(route('front.finish_checkout', $order->invoice))
                ->cookie($cookie);

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function checkoutFinish($invoice)
    {   
        $order = Order::with(['district'])
            ->where('invoice', 'LIKE', '%' . $invoice . '%')
            ->first();

        return view($this->path . '.checkout_finish', compact('order'));
    }

}
