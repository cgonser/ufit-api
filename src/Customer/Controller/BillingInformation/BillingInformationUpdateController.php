<?php

declare(strict_types=1);

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\BillingInformationDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\BillingInformationProvider;
use App\Customer\Request\BillingInformationRequest;
use App\Customer\ResponseMapper\BillingInformationResponseMapper;
use App\Customer\Service\BillingInformationRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class BillingInformationUpdateController extends AbstractController
{
    private BillingInformationRequestManager $billingInformationManager;

    private BillingInformationProvider $billingInformationProvider;

    private BillingInformationResponseMapper $billingInformationResponseMapper;

    public function __construct(
        BillingInformationRequestManager $billingInformationManager,
        BillingInformationProvider $billingInformationProvider,
        BillingInformationResponseMapper $billingInformationResponseMapper
    ) {
        $this->billingInformationManager = $billingInformationManager;
        $this->billingInformationProvider = $billingInformationProvider;
        $this->billingInformationResponseMapper = $billingInformationResponseMapper;
    }

    /**
     * @Route(
     *     "/customers/{customerId}/billing_information/{billingInformationId}",
     *     methods="PUT",
     *     name="customer_billing_information_update"
     * )
     * @ParamConverter("billingInformationRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=BillingInformationRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=BillingInformationDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function update(
        string $customerId,
        string $billingInformationId,
        BillingInformationRequest $billingInformationRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $billingInformation = $this->billingInformationProvider->getByCustomerAndId(
            $customer->getId(),
            Uuid::fromString($billingInformationId)
        );

        $billingInformationRequest->customerId = $customer->getId();
        $this->billingInformationManager->updateFromRequest($billingInformation, $billingInformationRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->billingInformationResponseMapper->map($billingInformation)
        );
    }
}
