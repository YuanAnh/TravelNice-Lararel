<?php

return [

    'vnpay' => [
        'tmn_code'    => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url'  => env('VNPAY_RETURN_URL'),
        'version'     => '2.1.0',
        'command'     => 'pay',
        'curr_code'   => 'VND',
        'locale'      => 'vn',
    ],

    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key'   => env('MOMO_ACCESS_KEY'),
        'secret_key'   => env('MOMO_SECRET_KEY'),
        'endpoint'     => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'return_url'   => env('MOMO_RETURN_URL'),
        'notify_url'   => env('MOMO_NOTIFY_URL'),
        'request_type' => 'payWithMethod',
    ],

];