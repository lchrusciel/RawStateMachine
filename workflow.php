<?php

use App\Model\Payment;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

require __DIR__ . '/vendor/autoload.php';

$definitionBuilder = new DefinitionBuilder();
$definition = $definitionBuilder->addPlaces(['new','pending','failed','paid'])
    // Transitions are defined with a unique name, an origin place and a destination place
    ->addTransition(new Transition('process', 'new', 'pending'))
    ->addTransition(new Transition('fail', 'pending', 'failed'))
    ->addTransition(new Transition('pay', 'pending', 'paid'))
    ->build()
;

$singleState = true;
$property = 'state';
$marking = new MethodMarkingStore($singleState, $property);
$workflow = new Workflow($definition, $marking);

$payment = new Payment();

$workflow->apply($payment, 'process');
$workflow->apply($payment, 'fail');

var_dump($payment);
