<?php

namespace App\Customer\Message;

use Ramsey\Uuid\UuidInterface;

class CustomerCreatedEvent
{
    public const NAME = 'customer.created';

    protected ?UuidInterface $customerId = null;

    public function __construct(UuidInterface $id)
    {
        $this->customerId = $id;
    }

    public function getCustomerId(): ?UuidInterface
    {
        return $this->customerId;
    }
}
