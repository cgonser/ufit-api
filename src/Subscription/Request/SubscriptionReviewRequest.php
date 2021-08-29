<?php

declare(strict_types=1);

namespace App\Subscription\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="SubscriptionReviewRequest"
 * )
 */
class SubscriptionReviewRequest
{
    /**
     * @OA\Property()
     * @Assert\NotNull
     */
    public bool $isApproved;

    /**
     * @OA\Property()
     */
    public ?string $reviewNotes = null;
}
