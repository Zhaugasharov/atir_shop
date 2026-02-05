<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $table = 'orders';

    protected $fillable = ['order_id', 'phone', 'product_id_1', 'product_id_2', 'product_id_3'];

    const STATUS = [
        1 => 'Новый',
        2 => 'Выбран',
    ];

    public function getStatus() {

        if(empty(self::STATUS[$this->status]))
            return '';

        return self::STATUS[$this->status];
    }

    public function product1()
    {
        return $this->hasOne(Product::class, 'id', 'product_id_1');
    }

    public function product2()
    {
        return $this->hasOne(Product::class, 'id', 'product_id_2');
    }

    public function product3()
    {
        return $this->hasOne(Product::class, 'id', 'product_id_3');
    }
}
