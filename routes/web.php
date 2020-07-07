<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Ecommerce'], function () {
    Route::get('/', 'FrontController@index')->name('front.index');
    Route::get('/product', 'FrontController@product')->name('front.product');
    Route::get('/product/{slug}', 'FrontController@show')->name('front.show_product');
    Route::get('/category/{slug}', 'FrontController@categoryProduct')->name('front.category');
    Route::post('/cart', 'CartController@addToCart')->name('front.cart');
    Route::get('/cart', 'CartController@listCart')->name('front.list_cart');
    Route::post('/cart/update', 'CartController@updateCart')->name('front.update_cart');
    Route::get('/cart/checkout', 'CartController@checkout')->name('front.checkout_cart');
    Route::post('/checkout', 'CartController@processCheckout')->name('front.store_checkout');
    Route::get('/checkout/{invoice}', 'CartController@checkoutFinish')->name('front.finish_checkout');
});

Route::group(['prefix' => 'member', 'namespace' => 'Ecommerce'], function () {
    Route::get('login', 'LoginController@loginForm')->name('customer.login');
    Route::get('verify/{token}', 'FrontController@verifyCustomerRegistration')->name('customer.verify');
    Route::post('login', 'LoginController@login')->name('customer.post_login');
    
    Route::group(['middleware' => 'customer'], function () {
        Route::get('dashboard', 'LoginController@dashboard')->name('customer.dashboard');
        Route::get('logout', 'LoginController@logout')->name('customer.logout');
        Route::get('orders', 'OrderController@index')->name('customer.orders');
        Route::get('orders/{invoice}', 'OrderController@view')->name('customer.view_order');
        Route::get('orders/pdf/{invoice}', 'OrderController@pdf')->name('customer.order_pdf');
        
        Route::get('payment', 'OrderController@paymentForm')->name('customer.payment_form');
        Route::post('payment', 'OrderController@storePayment')->name('customer.payment_save');

        Route::get('setting', 'FrontController@customerSettingForm')->name('customer.setting_form');
        Route::post('setting', 'FrontController@customerUpdateProfile')->name('customer.setting');

        Route::post('orders/accept', 'OrderController@acceptOrder')->name('customer.order_accept');
        Route::get('orders/return/{invoice}', 'OrderController@returnForm')->name('customer.order_return');
        Route::put('orders/return/{invoice}', 'OrderController@processReturn')->name('customer.return');

    });
});

Auth::routes();

Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('/category', 'CategoryController')->except(['create', 'show']);
    Route::resource('/product', 'ProductController')->except(['show']);
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk');
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.save_bulk');
    
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'AdminOrderController@index')->name('orders.index');
        Route::delete('/{id}', 'AdminOrderController@destroy')->name('orders.destroy');
        Route::get('/{invoice}', 'AdminOrderController@view')->name('orders.view');
        Route::get('/payment/{invoice}', 'AdminOrderController@acceptPayment')->name('orders.approve_payment');
        
        Route::post('/shipping', 'AdminOrderController@shippingOrder')->name('orders.shipping');
    });
    
});

