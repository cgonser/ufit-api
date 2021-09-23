<?php

declare(strict_types=1);

namespace App\Payment\Request;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Uuid;
use App\Core\Request\AbstractRequest;
use App\Customer\Request\BillingInformationRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class PaymentRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    #[NotNull]
    #[Uuid]
    public ?string $invoiceId = null;

    /**
     * @OA\Property()
     */
    #[NotNull]
    public ?string $paymentMethodId = null;

    /**
     * @OA\Property()
     */
    public ?string $billingInformationId = null;

    /**
     * @OA\Property(ref=@Model(type=BillingInformationRequest::class))
     */
    public ?BillingInformationRequest $billingInformation = null;

    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="card_hash", type="string")
     * )
     */
    public ?array $details = null;
}
