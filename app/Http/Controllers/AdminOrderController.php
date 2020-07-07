<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminOrderController extends Controller
{
    protected $path  = 'orders';

    public function index() 
    {
        $orders = Order::with(['customer'])->latest('created_at');
        
        if (request()->q != '') {
            $orders = $orders->where(function($q){
                $q->where('customer_name', 'LIKE', '%' . request()->q . '%')
                ->orWhere('invoice', 'LIKE', '%' . request()->q . '%')
                ->orWhere('customer_address', 'LIKE', '%' . request()->q . '%');
            }); 
        }

        if (request()->status != '') {
            $orders = $orders->where('status', request()->status);
        }
        
        $orders = $orders->paginate(10);

        return view($this->path . '.index', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->details->delete();
        $order->payment->delete();
        $order->delete();

        return redirect(route($this->path . '.index'));
    }

    public function view($invoice)
    {
        $order = Order::with(['customer', 'payment', 'details'])
            ->where('invoice', $invoice)->first();

        return view($this->path . '.view', compact('order'));
    }

    public function acceptPayment($invoice)
    {
        $order = Order::with(['payment'])->where('invoice', $invoice)->first();

        // PERHATIKAN PADA OBJECT PAYMENT
        $order->payment()->update(['status' => 1]);
        $order->update(['status' => 2]);

        return redirect(route('orders.view', $order->invoice));
    }

    public function shippingOrder(Request $request)
    {
        $order = Order::with(['customer'])->find($request->order_id);

        $order->update([
            'tracking_number' => $request->tracking_number,
            'status' => 3
        ]);

        Mail::to($order->customer->email)->send(new OrderMail($order));

        return redirect()->back();
    }

}
