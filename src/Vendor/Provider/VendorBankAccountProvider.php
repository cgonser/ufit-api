<?php

namespace App\Vendor\Provider;

use App\Core\Provider\AbstractProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Exception\VendorBankAccountNotFoundException;
use App\Vendor\Repository\VendorBankAccountRepository;
use Ramsey\Uuid\UuidInterface;

class VendorBankAccountProvider extends AbstractProvider
{
    public function __construct(VendorBankAccountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByVendorAndId(UuidInterface $vendorId, UuidInterface $vendorBankAccountId): VendorBankAccount
    {
        /** @var VendorBankAccount|null $vendorBankAccount */
        $vendorBankAccount = $this->repository->findOneBy([
            'id' => $vendorBankAccountId,
            'vendorId' => $vendorId,
        ]);

        if (!$vendorBankAccount) {
            $this->throwNotFoundException();
        }

        return $vendorBankAccount;
    }

    public function getOneByVendorId(UuidInterface $vendorId)
    {
        /** @var VendorBankAccount|null $vendorBankAccount */
        $vendorBankAccount = $this->repository->findOneBy([
            'vendorId' => $vendorId,
        ], [
            'createdAt' => 'DESC',
        ]);

        if (!$vendorBankAccount) {
            $this->throwNotFoundException();
        }

        return $vendorBankAccount;
    }

    protected function throwNotFoundException()
    {
        throw new VendorBankAccountNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return [
            'vendorId',
        ];
    }
}
