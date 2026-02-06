<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        if(empty($orderModel->id)) {
            $orderModel = Order::create(['order_id' => $orderId,
                                        'status' => 1,
                                        'phone' => '',
                                        'product_id_1' => 0,
                                        'product_id_2' => 0,
                                        'product_id_3' => 0]);
        }

        $data['orderId'] = $orderId;

        return view('order', $data);
    }

    public function saveOrder($orderId, Request $request) {
        $orderModel = Order::where('order_id', $orderId)->firstOrFail();

        $request->validate([
            'product_1' => ['required', 'integer', 'exists:product,id'],
            'product_2' => ['required', 'integer', 'exists:product,id'],
            'product_3' => ['required', 'integer', 'exists:product,id']
        ]);

        $orderModel->update([
            'status' => 2,
            'product_id_1' => $request->product_1,
            'product_id_2' => $request->product_2,
            'product_id_3' => $request->product_3
        ]);

        return redirect()->route('orderFinish', ['orderId' => $orderId]);
    }

    public function orderFinish($orderId) {
        $orderModel = Order::where(['order_id' => $orderId, 'status' => 2])->firstOrFail();
        $data = [];
        $data['order'] = $orderModel;
        return view('orderFinish', $data);
    }

}
