<?php

namespace App\Http\Controllers\Business;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\OrderRequest;
use App\Models\Business\Order;
use App\Models\ProductSku;
use App\Services\BusinessOrderService;
use App\Services\CartService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    public function index(Request $request)
    {
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('business.orders.index', ['orders' => $orders]);
    }


    public function create(Request $request)
    {
        $cartItems = $this->cartService->get();


        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }


    public function store(OrderRequest $request, BusinessOrderService $orderService)
    {
        $user = $request->user();

        $request = $request->only('remark', 'address', 'area', 'items', 'phone');

        $address = [
            'address' => $request['address'],
            'area' => $request['area'],
            'phone' => $request['phone'],
        ];

        return $orderService->store($user, $address, $request['remark'], $request['items']);

    }
}
