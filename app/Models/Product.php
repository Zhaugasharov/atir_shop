<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {

    use SoftDeletes; // <-- Добавляем трейт

    protected $table = 'product';

    protected $fillable = ['name', 'article', 'image', 'gender'];

    protected $dates = ['deleted_at'];

    public function keywords()
    {
        return $this->belongsToMany(KeyWord::class, 'product_keyword', 'product_id', 'keyword_id');
    }

    // Аксессор для получения URL изображения
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default.jpg'); // Дефолтное изображение
    }

    // Аксессор для получения строки ключевых слов
    public function getKeywordsStringAttribute()
    {
        return $this->keywords->pluck('name')->implode(', ');
    }
}
