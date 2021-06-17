<?php

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
            'customerId' => 'subscription',
            'vendorId' => 'vendorPlan',
            'invoiceId',
            'paymentMethodId',
            'status',
        ];
    }
}
