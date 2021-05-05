<?php

namespace App\Payment\Service\PaymentProcessor;

use Ramsey\Uuid\UuidInterface;

interface VendorInformationManagerInterface
{
    public function updateVendorInformation(UuidInterface $vendorId);
}
