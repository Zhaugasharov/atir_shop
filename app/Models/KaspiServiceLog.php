<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaspiServiceLog extends Model
{
    protected $table = 'kaspi_service_logs';

    protected $fillable = [
        'status',
        'message',
    ];

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const STATUS_WARNING = 'warning';

    public function scopeErrors($query)
    {
        return $query->where('status', self::STATUS_ERROR);
    }

    public function scopeLatestErrors($query, int $limit = 20)
    {
        return $query->errors()->orderByDesc('created_at')->limit($limit);
    }
}
