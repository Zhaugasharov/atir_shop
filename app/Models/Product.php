<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {

    use SoftDeletes; // <-- Добавляем трейт

    protected $table = 'product';

    protected $fillable = ['name', 'article', 'image', 'gender', 'brand_id', 'is_new'];

    protected $appends = ['image_url'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_new' => 'boolean',
    ];

    public function keywords()
    {
        return $this->belongsToMany(KeyWord::class, 'product_keyword', 'product_id', 'keyword_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Аксессор для получения URL изображения
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset($this->image);
        }
        return asset('images/default.jpg'); // Дефолтное изображение
    }

    // Аксессор для получения строки ключевых слов
    public function getKeywordsStringAttribute()
    {
        return $this->keywords->pluck('name')->implode(', ');
    }
}
