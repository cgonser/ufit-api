<?php

declare(strict_types=1);

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\BillingInformationDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\BillingInformationRequest;
use App\Customer\ResponseMapper\BillingInformationResponseMapper;
use App\Customer\Service\BillingInformationRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/customers/{customerId}/billing_information')]
class BillingInformationCreateController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private BillingInformationResponseMapper $billingInformationResponseMapper,
        private BillingInformationRequestManager $billingInformationRequestManager
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=BillingInformationRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=BillingInformationDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     * @Security(name="Bearer")
     */
    #[Route(name: 'customer_billing_information_create', methods: 'POST')]
    #[ParamConverter(data: 'billingInformationRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function create(
        string $customerId,
        BillingInformationRequest $billingInformationRequest,
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $billingInformationRequest->customerId = $customer->getId()->toString();
        $billingInformation = $this->billingInformationRequestManager->createFromRequest($billingInformationRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->billingInformationResponseMapper->map($billingInformation)
        );
    }
}
