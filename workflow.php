<?php

use App\EventListener\BlockedGuardListener;
use App\EventListener\PaymentPaidListener;
use App\Model\Payment;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

require __DIR__ . '/vendor/autoload.php';

$definitionBuilder = new DefinitionBuilder();
$definition = $definitionBuilder->addPlaces(['new','pending','failed','paid','blocked'])
    // Transitions are defined with a unique name, an origin place and a destination place
    ->addTransition(new Transition('process', 'new', 'pending'))
    ->addTransition(new Transition('fail', 'pending', 'failed'))
    ->addTransition(new Transition('pay', 'pending', 'paid'))
    ->addTransition(new Transition('block', 'pending', 'blocked'))
    ->build()
;

$singleState = true;
$property = 'state';
$marking = new MethodMarkingStore($singleState, $property);

$dispatcher = new EventDispatcher();
$dispatcher->addListener('workflow.payment.enter.paid', new PaymentPaidListener());
$dispatcher->addListener(
    'workflow.payment.guard.block',
    new BlockedGuardListener()
);

$workflow = new Workflow($definition, $marking, $dispatcher, 'payment');

$payment = new Payment();

$workflow->apply($payment, 'process');
$workflow->apply($payment, 'block');

var_dump($payment);
