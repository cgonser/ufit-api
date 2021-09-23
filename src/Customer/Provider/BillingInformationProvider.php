<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Core\Provider\AbstractProvider;
use App\Customer\Entity\BillingInformation;
use App\Customer\Exception\BillingInformationNotFoundException;
use App\Customer\Repository\BillingInformationRepository;
use Ramsey\Uuid\UuidInterface;

class BillingInformationProvider extends AbstractProvider
{
    public function __construct(BillingInformationRepository $billingInformationRepository)
    {
        $this->repository = $billingInformationRepository;
    }

    public function getByCustomerAndId(
        UuidInterface $customerId,
        UuidInterface $billingInformationId
    ): BillingInformation {
        /** @var BillingInformation|null $billingInformation */
        $billingInformation = $this->repository->findOneBy([
            'id' => $billingInformationId,
            'customerId' => $customerId,
        ]);

        if ($billingInformation === null) {
            throw new BillingInformationNotFoundException();
        }

        return $billingInformation;
    }

    protected function throwNotFoundException(): void
    {
        throw new BillingInformationNotFoundException();
    }

    /**
     * @return string[]
     */
    protected function getFilterableFields(): array
    {
        return ['customerId'];
    }
}
