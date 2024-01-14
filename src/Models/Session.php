<?php

namespace Laiux\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laiux\Auth\Casts\Session\IpInfoCast;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'issued_date',
        'expire_time',
        'expire_date',
        'device',
        'platform',
        'browser',
        'is_desktop',
        'is_phone',
        'is_robot',
        'ip_info'
    ];

    protected $casts = [
        'issued_date' => 'integer',
        'expire_time' => 'integer',
        'expire_date' => 'integer',
        'is_desktop' => 'boolean',
        'is_phone' => 'boolean',
        'is_robot' => 'boolean',
        'ip_info' => IpInfoCast::class,
    ];
}
