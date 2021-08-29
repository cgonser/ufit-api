<?php

declare(strict_types=1);

namespace App\Vendor\Provider;

use App\Core\Provider\AbstractProvider;
use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Exception\VendorBankAccountNotFoundException;
use App\Vendor\Repository\VendorBankAccountRepository;
use Ramsey\Uuid\UuidInterface;

class VendorBankAccountProvider extends AbstractProvider
{
    public function __construct(VendorBankAccountRepository $vendorBankAccountRepository)
    {
        $this->repository = $vendorBankAccountRepository;
    }

    public function getByVendorAndId(UuidInterface $vendorId, UuidInterface $vendorBankAccountId): ?VendorBankAccount
    {
        /** @var VendorBankAccount|null $vendorBankAccount */
        $vendorBankAccount = $this->repository->findOneBy([
            'id' => $vendorBankAccountId,
            'vendorId' => $vendorId,
        ]);

        if (null === $vendorBankAccount) {
            $this->throwNotFoundException();
        }

        return $vendorBankAccount;
    }

    public function getOneByVendorId(UuidInterface $vendorId): ?VendorBankAccount
    {
        /** @var VendorBankAccount|null $vendorBankAccount */
        $vendorBankAccount = $this->repository->findOneBy([
            'vendorId' => $vendorId,
        ], [
            'createdAt' => 'DESC',
        ]);

        if (null === $vendorBankAccount) {
            $this->throwNotFoundException();
        }

        return $vendorBankAccount;
    }

    protected function throwNotFoundException(): void
    {
        throw new VendorBankAccountNotFoundException();
    }

    /**
     * @return string[]
     */
    protected function getFilterableFields(): array
    {
        return ['vendorId'];
    }
}
