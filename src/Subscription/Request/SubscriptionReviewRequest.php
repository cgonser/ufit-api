<?php

declare(strict_types=1);

namespace App\Subscription\Request;

use Symfony\Component\Validator\Constraints\NotNull;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class SubscriptionReviewRequest
{
    /**
     * @OA\Property()
     */
    #[NotNull]
    public bool $isApproved;

    /**
     * @OA\Property()
     */
    public ?string $reviewNotes = null;
}
