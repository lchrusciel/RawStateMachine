<?php

use App\Model\Payment;
use App\Operator\InventoryOperator;
use SM\StateMachine\StateMachine;

require __DIR__ . '/vendor/autoload.php';

$config = [
    'graph' => 'payment',
    'property_path' => 'state',
    'states' => ['new','pending','failed','paid'],
    'transitions' => [
        'process' => [
            'from' => ['new'],
            'to' => 'pending',
        ],
        'fail' => [
            'from' => ['pending'],
            'to' => 'failed',
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
    ],
];

$payment = new Payment();

$stateMachine = new StateMachine($payment, $config);

$stateMachine->apply('process');
$stateMachine->apply('pay');

var_dump($payment);
