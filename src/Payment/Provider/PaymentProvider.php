<?php

declare(strict_types=1);

namespace App\Payment\Provider;

use App\Core\Provider\AbstractProvider;
use App\Payment\Entity\Payment;
use App\Payment\Exception\PaymentNotFoundException;
use App\Payment\Repository\PaymentRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class PaymentProvider extends AbstractProvider
{
    public function __construct(PaymentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByCustomerAndId(UuidInterface $customerId, UuidInterface $paymentId): Payment
    {
        /** @var Payment|null $payment */
        $payment = $this->get($paymentId);

        if (! $payment->getInvoice()->getSubscription()->getCustomerId()->equals($customerId)) {
            $this->throwNotFoundException();
        }

        return $payment;
    }

    public function getByExternalReference(string $externalReference): Payment
    {
        return $this->getBy([
            'externalReference' => $externalReference,
        ]);
    }

    protected function throwNotFoundException()
    {
        throw new PaymentNotFoundException();
    }

    protected function buildQueryBuilder(): QueryBuilder
    {
        return parent::buildQueryBuilder()
            ->innerJoin('root.invoice', 'invoice')
            ->innerJoin('invoice.subscription', 'subscription')
            ->innerJoin('subscription.vendorPlan', 'vendorPlan');
    }

    protected function getFilterableFields(): array
    {
        return [
            [
                'customerId' => 'subscription',
            ],
            [
                'vendorId' => 'vendorPlan',
            ],
            [
                'subscriptionId' => 'invoice',
            ],
            'invoiceId',
            'paymentMethodId',
            'status',
        ];
    }
}
