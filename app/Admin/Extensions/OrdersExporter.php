<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExporter extends ExcelExporter implements WithMapping
{

    protected $fileName = '订单.xlsx';

    protected $columns = [
        'no' => '订单流水号',
        'user_id' => '买家',
        'area' => '地区',
        'address' => '地址',
        'address.phone' => '电话',
        'created_at' => '下单时间',
        'total_amount' => '总金额',
        'ship_status' => '物流',
        'id' => '商品信息',
    ];


    public function map($order): array
    {


        $goosItems = $order->items()->with(['product', 'productSku'])->get();
        $goosItemsString = '';
        foreach ($goosItems as $item) {
            $item->productName = $item->product->title;
            $item->productSkuName = $item->productSku->title;
            $goosItemsString .= "商品-" . $item->productName . "--" . $item->productSkuName . "--数量--" . $item->amount . "\r\n";
        }

        return [
            $order->no,
            data_get($order, 'user.name'),
            $order->area,
            $order->address['address'],
            $order->address['phone'],
            $order->created_at,
            $order->total_amount,
            $order->ship_status,
            $order->item = $goosItemsString
        ];

    }
}
