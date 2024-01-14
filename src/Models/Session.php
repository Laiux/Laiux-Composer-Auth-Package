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

    protected $hidden = [
        'id'
    ];

    protected $casts = [
        'ip_info' => IpInfoCast::class,
    ];
}
