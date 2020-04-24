<?php

namespace App\Http\Controllers\Business;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\OrderRequest;
use App\Models\CouponCode;
use App\Models\Business\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\OrderService;
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

        return view('orders.index', ['orders' => $orders]);
    }


    public function create(Request $request)
    {
        $cartItems = $this->cartService->get();


        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function store(OrderRequest $request)
    {
        $user = $request->user();

        $request = $request->only('remark', 'address', 'area', 'items', 'phone');

        $order = new Order([
            'address' => [
                'address' => $request['address'],
                'area' => $request['area'],
                'phone' => $request['phone'],
            ],
            'remark' => $request['remark'],
            'total_amount' => 0,
        ]);
        $order->user()->associate($user);

        $order->save();

        $totalAmount = 0;
        foreach ($request['items'] as $data) {
            $sku = ProductSku::find($data['sku_id']);
            // 创建一个 OrderItem 并直接与当前订单关联
            $item = $order->items()->make([
                'amount' => $data['amount'],
                'price' => $sku->price,
            ]);
            $item->product()->associate($sku->product_id);

            $item->productSku()->associate($sku);

            $item->save();

            $totalAmount += $sku->price * $data['amount'];
            if ($sku->decreaseStock($data['amount']) <= 0) {
                throw new InvalidRequestException('该商品库存不足');
            }
        }
        return $totalAmount;
    }
}
