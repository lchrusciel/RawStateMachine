<?php

use App\Authorizer\BlockedAuthorizer;
use App\Model\Payment;
use App\Operator\InventoryOperator;
use SM\StateMachine\StateMachine;

require __DIR__ . '/vendor/autoload.php';

$config = [
    'graph' => 'payment',
    'property_path' => 'state',
    'states' => ['new','pending','failed','paid', 'blocked'],
    'transitions' => [
        'process' => [
            'from' => ['new'],
            'to' => 'pending',
        ],
        'fail' => [
            'from' => ['pending'],
            'to' => 'failed',
        ],
        'block' => [
            'from' => ['pending'],
            'to' => 'blocked',
        ],
        'pay' => [
            'from' => ['pending'],
            'to' => 'paid',
        ],
    ],
    'callbacks' => [
        'before' => [
            'reduce_amount' => [
                'on' => ['pay'],
                'do' => new InventoryOperator(),
            ],
        ],
        'guard' => [
            'guard-blocked' => [
                'to' => ['blocked'],
                'do' => new BlockedAuthorizer(),
            ],
        ],
    ],
];

$payment = new Payment();

$stateMachine = new StateMachine($payment, $config);

$stateMachine->apply('process');
$stateMachine->apply('block');

var_dump($payment);
