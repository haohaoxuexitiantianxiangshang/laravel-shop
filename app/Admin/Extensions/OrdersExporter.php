<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
class OrdersExporter extends ExcelExporter implements WithMapping
{

    protected $fileName = '订单.xlsx';

    public function headings(): array
    {
        return [
            '订单流水号',
            '买家',
            '地区',
            '地址',
            '电话',
            '下单时间',
            '总金额',
            '备注',
            '物流',
            '涨芝士',
            '老酸奶',
            '简醇',
            '白小纯',
            '鲜活牛乳',
            '优果酪',
            '白小纯燕麦',
            '芝芝好莓',
            '芝芝好芒'
        ];
    }


    public function map($order): array
    {
        $goosItems = $order->items()->with(['product', 'productSku'])->get();
        $goosItemsObject = (object)[];

        foreach ($goosItems as $item) {
            $productName = $item->product->title;
            $productSkuName = $item->productSku->title;
            $goosItemsObject->$productName = $item->amount;
        }
        return [
            $order->no,
            data_get($order, 'user.name'),
            $order->area,
            $order->address['address'],
            $order->address['phone'],
            $order->created_at,
            $order->total_amount,
            $order->remark,
            $order->ship_status,
            data_get($goosItemsObject, '涨芝士', 0),
            data_get($goosItemsObject, '老酸奶', 0),
            data_get($goosItemsObject, '简醇', 0),
            data_get($goosItemsObject, '白小纯', 0),
            data_get($goosItemsObject, '鲜活牛乳', 0),
            data_get($goosItemsObject, '优果酪', 0),
            data_get($goosItemsObject, '白小纯燕麦', 0),
            data_get($goosItemsObject, '芝芝好莓', 0),
            data_get($goosItemsObject, '芝芝好芒', 0)
        ];

    }
}
