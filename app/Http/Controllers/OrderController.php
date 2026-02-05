<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class OrderController extends Controller {

    function order($orderId) {
        $data = [];

        return view('order', $data);
    }

}
