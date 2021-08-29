<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

class VendorSettingDto
{
    public string $id;

    public ?string $vendorId = null;

    public ?string $name = null;

    public ?string $value = null;
}
