<?php

namespace App\Admin\Controllers;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Models\Business\Order;
use App\Models\ProductSku;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Form;
use App\Admin\Extensions\OrdersExporter;
class BusinessOrdersController extends AdminController
{
    use ValidatesRequests;

    protected $title = '订单';

    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->model()->orderBy('id', 'desc');

        $grid->no('订单流水号');
        // 展示关联关系的字段时，使用 column 方法
        $grid->column('user.name', '买家');

        $grid->area('地区')->sortable();

        $grid->column("address->address", '地址');

        $grid->column("address->phone", '电话');

        $grid->column("remark", '备注');

        $grid->column('created_at', '下单时间')->sortable();


        $grid->total_amount('总金额')->expand(function ($model) {

            $sku = $model->items()->with(['product', 'productSku'])->get()->map(function ($sku) {
                $sku->productName = $sku->product->title;
                $sku->productSkuName = $sku->productSku->title;
                return $sku->only(['productName', 'productSkuName', 'amount', 'price']);
            });

            return new Table(['商品名', '规格', '数量', '价格'], $sku->toArray());
        });

        $grid->ship_status('物流');

        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();

        //表单导出
        $grid->exporter(new OrdersExporter());
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('no', '订单号');
            $filter->like('area', '地区');
            $filter->like('user.name', '买家');
            $filter->between('created_at', "下单时间")->datetime();
        });
        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('查看订单')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.business.orders.show', ['order' => Order::find($id)]));
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }

    public function form()
    {
        $form = new Form(new Order);
        $form->display('id', 'ID');
        $form->display('no', '订单流水号');
        $form->display('user.name', '买家');
        $form->display('created_at', '下单时间');
        $form->display('total_amount', '总金额');
        $form->embeds('address', '送货信息', function ($form) {
            $form->text('address', '地址')->rules('required');
            $form->text('area', '区域')->rules('required');
            $form->mobile('phone', '电话');
        });

        $form->text('remark', '备注');

        return $form;
    }

}
