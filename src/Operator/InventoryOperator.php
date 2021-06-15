<?php

declare(strict_types=1);

namespace App\Operator;

use App\Model\Payment;
use SM\Event\TransitionEvent;

final class InventoryOperator
{
    public function __invoke(TransitionEvent $transitionEvent): void
    {
        $stateMachine = $transitionEvent->getStateMachine();
        /** @var Payment $payment */
        $payment = $stateMachine->getObject();

        var_dump('Dispatched on transition');
        var_dump($payment);
    }
}
