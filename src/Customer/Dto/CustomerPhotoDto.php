<?php

declare(strict_types=1);

namespace App\Customer\Dto;

class CustomerPhotoDto
{
    public string $id;

    public string $customerId;

    public string $title;

    public string $description;

    public string $url;

    public string $takenAt;
}
