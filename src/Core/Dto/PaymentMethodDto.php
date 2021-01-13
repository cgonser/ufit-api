<?php

namespace App\Core\Dto;

class PaymentMethodDto
{
    public string $id;

    public string $name;

    public ?array $countriesEnabled = null;

    public ?array $countriesDisabled = null;

    public bool $isActive;
}
