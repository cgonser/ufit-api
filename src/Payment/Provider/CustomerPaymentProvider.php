<?php

namespace App\Payment\Provider;

use App\Core\Request\SearchRequest;
use App\Payment\Entity\Payment;
use App\Customer\Entity\Customer;
use App\Payment\Request\CustomerPaymentSearchRequest;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class CustomerPaymentProvider extends PaymentProvider
{
    public function getByCustomerAndId(UuidInterface $customerId, UuidInterface $paymentId): Payment
    {
        /** @var Payment|null $payment */
        $payment = $this->get($paymentId);

        if (!$payment->getInvoice()->getSubscription()->getCustomerId()->equals($customerId)) {
            $this->throwNotFoundException();
        }

        return $payment;
    }

    protected function buildQueryBuilder(): QueryBuilder
    {
        return parent::buildQueryBuilder()
            ->innerJoin('root.invoice', 'invoice')
            ->innerJoin('invoice.subscription', 'subscription');
    }

    protected function getFilterableFields(): array
    {
        return [
            ['customerId' => 'subscription'],
            ['subscriptionId' => 'invoice'],
        ];
    }
}
