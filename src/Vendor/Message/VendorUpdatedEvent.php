<?php

declare(strict_types=1);

namespace App\Vendor\Message;

use Ramsey\Uuid\UuidInterface;

class VendorUpdatedEvent
{
    /**
     * @var string
     */
    public const NAME = 'vendor.updated';

    public function __construct(
        private UuidInterface $uuid
    ) {
    }

    public function getVendorId(): ?UuidInterface
    {
        return $this->uuid;
    }
}
