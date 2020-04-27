<?php

namespace App\Services;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\User;
use App\Models\Business\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;

class BusinessOrderService
{
    public function store(User $user, $address, $remark, $items)
    {
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $address, $remark, $items) {

            $order = new Order([
                'area' => $address['area'],
                'address' => $address,
                'remark' => $remark,
                'total_amount' => 0,
            ]);
            $order->user()->associate($user);
            $order->save();
            $totalAmount = 0;
            foreach ($items as $data) {
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
                $totalAmount = round($totalAmount);

                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }

            }

            if ($totalAmount <= 100) {
                throw new InvalidRequestException('商品总价为' . $totalAmount . '低于100元,无法派送');
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);
            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);
            return $order;
        });


        return $order;
    }
}
