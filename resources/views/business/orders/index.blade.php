@extends('layouts.app')
@section('title', '订单列表')

@section('content')
    <div class="row">
        <div class="card" style="width: 100%;">
            <div class="card-header">订单列表1</div>
            <ul class="list-group">
                @foreach($orders as $order)
                    <li style="padding: 0; display: block;">
                        <div class="card ">
                            <div class="card-header">
                                <p>订单号：{{ $order->no }}</p>
                                <p class="small">{{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>商品信息</th>
                                        <th class="text-center">单价</th>
                                        <th class="text-center">数量</th>
                                        <th class="text-center">订单总价</th>
                                    </tr>
                                    </thead>
                                    @foreach($order->items as $index => $item)
                                        <tr>
                                            <td class="product-info">
                                                <div>
                                                            <span class="product-title">
                                                               <a target="_blank"
                                                                  href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                                            </span>
                                                    <span class="sku-title">{{ $item->productSku->title }}</span>
                                                </div>
                                            </td>
                                            <td class="sku-price text-center">￥{{ $item->price }}</td>
                                            <td class="sku-amount text-center">{{ $item->amount }}</td>
                                            @if($index === 0)
                                                <td rowspan="{{ count($order->items) }}"
                                                    class="text-center total-amount">￥{{ $order->total_amount }}</td>


                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="float-right">{{ $orders->render() }}</div>

        </div>
    </div>
@endsection
