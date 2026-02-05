<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller {

    function order($orderId) {
        $data = [];
        $orderModel = Order::where('order_id', $orderId)->first();

        if(!empty($orderModel->id)) {
            if($orderModel->status == 2)
                return redirect()->route('orderFinish', ['orderId' => $orderModel->order_id]);
        }

        return view('order', $data);
    }

    public function orderFinish($orderId) {
        $orderModel = Order::where(['order_id' => $orderId, 'status' => 2])->firstOrFail();
        $data = [];
        $data['order'] = $orderModel;
        return view('orderFinish', $data);
    }

}
