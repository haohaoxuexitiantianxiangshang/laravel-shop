<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'BusinessOrdersController@index')->name('admin.business.orders.index');
    $router->get('users', 'UsersController@index');
    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');
//    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
//    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
//    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');
//    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund');
    $router->get('coupon_codes', 'CouponCodesController@index');
    $router->post('coupon_codes', 'CouponCodesController@store');
    $router->get('coupon_codes/create', 'CouponCodesController@create');
    $router->get('coupon_codes/{id}/edit', 'CouponCodesController@edit');
    $router->put('coupon_codes/{id}', 'CouponCodesController@update');
    $router->delete('coupon_codes/{id}', 'CouponCodesController@destroy');


    $router->get('business/orders', 'BusinessOrdersController@index')->name('admin.business.orders.index');
    $router->get('business/orders/{order}', 'BusinessOrdersController@show')->name('admin.business.orders.show');
    $router->get('business/orders/{order}/edit', 'BusinessOrdersController@edit')->name('admin.business.orders.edit');
    $router->put('business/orders/{id}', 'BusinessOrdersController@update');
});
