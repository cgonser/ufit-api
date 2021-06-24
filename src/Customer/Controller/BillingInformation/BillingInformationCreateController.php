<?php

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\BillingInformationDto;
use App\Customer\Entity\Customer;
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

class BillingInformationCreateController extends AbstractController
{
    private BillingInformationResponseMapper $billingInformationResponseMapper;

    private BillingInformationRequestManager $billingInformationManager;

    public function __construct(
        BillingInformationResponseMapper $billingInformationResponseMapper,
        BillingInformationRequestManager $billingInformationManager
    ) {
        $this->billingInformationResponseMapper = $billingInformationResponseMapper;
        $this->billingInformationManager = $billingInformationManager;
    }

    /**
     * @Route("/customers/{customerId}/billing_information", methods="POST", name="customer_billing_information_create")
     * @ParamConverter("billingInformationRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=BillingInformationRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=BillingInformationDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function create(
        string $customerId,
        BillingInformationRequest $billingInformationRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $billingInformationRequest->customerId = $customer->getId();
        $billingInformation = $this->billingInformationManager->createFromRequest($billingInformationRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->billingInformationResponseMapper->map($billingInformation)
        );
    }
}
