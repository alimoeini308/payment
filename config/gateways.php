<?php
return [
    'available' => explode(',', env('GATEWAYS', 'zarinpal,shepa,zibal')),
    'max_amount' => env('MAX_AMOUNT', '100000'),
    'default' => env('DEFAULT_GATEWAY', 'zarinpal'),
    'fields' => [
        'zarinpal' => [
            'token' => 'Authority',
            'status' => 'Status',
            'success_value' => 'OK'
        ],
        'shepa' => [
            'token' => 'token',
            'status' => 'status',
            'success_value' => 'success'
        ],
        'zibal' => [
            'token' => 'trackId',
            'status' => 'success',
            'success_value' => '1'
        ],
    ]
];
