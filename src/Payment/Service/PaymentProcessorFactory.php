<?php

declare(strict_types=1);

namespace App\Payment\Service;

use App\Payment\Entity\PaymentMethod;
use App\Payment\Exception\PaymentProcessorNotFoundException;
use App\Payment\Service\PaymentProcessor\PaymentProcessorInterface;

class PaymentProcessorFactory
{
    /**
     * @param PaymentProcessorInterface[] $processors
     */
    public function __construct(private iterable $processors)
    {
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
