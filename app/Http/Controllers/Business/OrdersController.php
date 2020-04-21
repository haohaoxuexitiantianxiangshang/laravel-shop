<?php

namespace App\Http\Controllers\Business;

use App\Exceptions\CouponCodeUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\CouponCode;
use App\Models\Order;
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

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $coupon = null;

        // 如果用户提交了优惠码
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code', $code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }
        // 参数中加入 $coupon 变量
        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'), $coupon);
    }
}
