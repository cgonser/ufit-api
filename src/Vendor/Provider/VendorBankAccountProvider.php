<?php

namespace App\Vendor\Provider;

use App\Core\Provider\AbstractProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorBankAccountNotFoundException;
use App\Vendor\Repository\VendorBankAccountRepository;
use App\Vendor\Entity\VendorBankAccount;
use Ramsey\Uuid\UuidInterface;

class VendorBankAccountProvider extends AbstractProvider
{
    public function __construct(VendorBankAccountRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getByVendorAndId(Vendor $vendor, UuidInterface $vendorBankAccountId): VendorBankAccount
    {
        /** @var VendorBankAccount|null $vendorBankAccount */
        $vendorBankAccount = $this->repository->findOneBy([
            'id' => $vendorBankAccountId,
            'vendor' => $vendor,
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
