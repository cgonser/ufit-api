<?php

namespace App\Payment\Service;

use App\Core\Entity\PaymentMethod;
use App\Payment\Exception\PaymentProcessorNotFoundException;
use App\Payment\Service\PaymentProcessor\PaymentProcessorInterface;

class PaymentProcessorFactory
{
    /** @var Iterable<App\Payment\Service\PaymentProcessor\PaymentProcessorInterface> */
    private iterable $processors;

    public function __construct(iterable $processors)
    {
        $this->processors = $processors;
    }

    public function createProcessor(PaymentMethod $paymentMethod): PaymentProcessorInterface
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($paymentMethod)) {
                return $processor;
            }
        }

        throw new PaymentProcessorNotFoundException($paymentMethod->getName());
    }
}