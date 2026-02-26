<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $table = 'message_templates';

    protected $fillable = ['name', 'body', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public static function getPlaceholders(): array
    {
        return [
            '{order_id}' => 'Номер заказа',
            '{order_link}' => 'Ссылка на страницу заказа',
            '{total_price}' => 'Сумма заказа',
            '{customer_name}' => 'Имя покупателя',
            '{phone}' => 'Телефон',
        ];
    }

    public static function getDefault(): ?self
    {
        $default = self::where('is_default', true)->first();
        return $default ?? self::first();
    }

    public function render(array $data): string
    {
        $body = $this->body;
        foreach ($data as $key => $value) {
            $body = str_replace('{' . $key . '}', (string) $value, $body);
        }
        return $body;
    }
}
