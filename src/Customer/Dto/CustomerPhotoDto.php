<?php

namespace App\Customer\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class CustomerPhotoDto
{
    public string $id;

    public string $customerId;

    public string $title;

    public string $description;

    public string $url;

    public string $takenAt;
}
