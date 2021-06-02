<?php

namespace App\Vendor\Message;

use Ramsey\Uuid\UuidInterface;

class VendorCreatedEvent
{
    public const NAME = 'vendor.created';

    protected ?UuidInterface $vendorId = null;

    public function __construct(UuidInterface $id)
    {
        $this->vendorId = $id;
    }

    public function getVendorId(): ?UuidInterface
    {
        return $this->vendorId;
    }
}
