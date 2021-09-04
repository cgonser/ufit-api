<?php

declare(strict_types=1);

namespace App\Payment\Provider;

use Doctrine\Common\Collections\Criteria;
use App\Core\Provider\AbstractProvider;
use App\Payment\Entity\Invoice;
use App\Payment\Exception\InvoiceNotFoundException;
use App\Payment\Repository\InvoiceRepository;
use Ramsey\Uuid\UuidInterface;

class InvoiceProvider extends AbstractProvider
{
    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->repository = $invoiceRepository;
    }

    public function getSubscriptionNextDueInvoice(UuidInterface $subscriptionId): Invoice
    {
        $invoice = $this->repository->createQueryBuilder('i')
            ->where('i.subscriptionId = :subscriptionId')
            ->andWhere('i.paidAt IS NULL')
            ->orderBy('i.dueDate', Criteria::ASC)
            ->setParameter('subscriptionId', $subscriptionId)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $invoice) {
            $this->throwNotFoundException();
        }

        return $invoice;
    }

    protected function throwNotFoundException(): void
    {
        throw new InvoiceNotFoundException();
    }

    /**
     * @return mixed[]
     */
    protected function getSearchableFields(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected function getFilterableFields(): array
    {
        return ['subscriptionId', 'currencyId'];
    }
}
