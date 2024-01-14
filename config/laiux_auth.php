<?php

return [

    'secret' => env('LAIUX_AUTH_SECRET', 'secret'),
    'url' => env('APP_URL', 'http://localhost'),
    'expiration_time' => env('LAIUX_AUTH_EXPIRATION_TIME', 3600),
    'algorithm' => env('LAIUX_ALGORITHM', 'HS256'),
    'ipinfo_access_token' => env('IPINFO_ACCESS_TOKEN')

];
