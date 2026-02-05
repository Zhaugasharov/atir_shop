<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $table = 'orders';

    protected $fillable = ['order_id', 'phone', 'product_id_1', 'product_id_2', 'product_id_3'];

    const STATUS = {
        1 => 'Новый',
        2 => 'Выбран',
    }
}
