<?php

namespace App\Core\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CurrencyRequest"
 * )
 */
class CurrencyRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $code = null;
}