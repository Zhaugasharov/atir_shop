<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastMessage extends Model
{
    protected $table = 'broadcast_messages';

    protected $fillable = [
        'order_id',
        'phone',
        'message',
        'waba_message_id',
        'delivery_status',
        'source',
    ];

    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';

    const SOURCE_KASPI = 'kaspi';
    const SOURCE_MANUAL = 'manual';

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_SENT => 'Отправлено',
            self::STATUS_DELIVERED => 'Доставлено',
            self::STATUS_FAILED => 'Не доставлено',
        ];
    }

    public function getStatusLabel(): string
    {
        return self::getStatusLabels()[$this->delivery_status] ?? $this->delivery_status;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
