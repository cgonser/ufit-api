<?php

namespace App\Payment\Provider;

use App\Core\Provider\AbstractProvider;
use App\Payment\Entity\Invoice;
use App\Payment\Exception\InvoiceNotFoundException;
use App\Payment\Repository\InvoiceRepository;
use Ramsey\Uuid\UuidInterface;

class InvoiceProvider extends AbstractProvider
{
    public function __construct(InvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getSubscriptionNextDueInvoice(UuidInterface $subscriptionId): Invoice
    {
        $invoice = $this->repository->createQueryBuilder('i')
            ->where('i.subscriptionId = :subscriptionId')
            ->andWhere('i.paidAt IS NULL')
            ->orderBy('i.dueDate', 'ASC')
            ->setParameter('subscriptionId', $subscriptionId)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $invoice) {
            $this->throwNotFoundException();
        }

        return $invoice;
    }

    protected function throwNotFoundException()
    {
        throw new InvoiceNotFoundException();
    }

    protected function getSearchableFields(): array
    {
        return [];
    }

    protected function getFilterableFields(): array
    {
        return [
            'subscriptionId',
            'currencyId',
        ];
    }
}
