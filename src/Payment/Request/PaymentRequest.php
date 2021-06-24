<?php

namespace App\Payment\Request;

use App\Core\Request\AbstractRequest;
use App\Customer\Request\BillingInformationRequest;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody()
 */
class PaymentRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     * @Assert\NotNull
     * @Assert\Uuid()
     */
    public ?string $invoiceId = null;

    /**
     * @OA\Property()
     * @Assert\NotNull
     */
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
