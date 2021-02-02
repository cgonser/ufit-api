<?php

namespace App\Customer\Dto;

use App\Subscription\Dto\SubscriptionDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class CustomerDto
{
    public string $id;

    public ?string $name;

    public ?string $email;

    public ?string $phone;

    public ?string $birthDate;

    public ?int $height;

    public ?string $gender;

    /**
     * @var string[]
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $goals;

    /**
     * @var SubscriptionDto[]
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=SubscriptionDto::class)))
     */
    public ?array $subscriptions;
}
