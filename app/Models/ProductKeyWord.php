<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductKeyWord extends Model {

    protected $table = 'product_keyword';

    protected $fillable = ['product_id', 'keyword_id'];

}
