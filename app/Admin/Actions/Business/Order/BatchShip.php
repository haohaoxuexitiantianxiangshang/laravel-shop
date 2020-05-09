<?php

namespace App\Admin\Actions\Business\Order;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchShip extends BatchAction
{
    public $name = '批量发货';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->ship_status = 'Shipped';
            $model->save();
        }

        return $this->response()->success('设置成功')->refresh();
    }

}
