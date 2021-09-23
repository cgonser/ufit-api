<?php

declare(strict_types=1);

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\BillingInformationDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\BillingInformationProvider;
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
class BillingInformationUpdateController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private BillingInformationRequestManager $billingInformationRequestManager,
        private BillingInformationProvider $billingInformationProvider,
        private BillingInformationResponseMapper $billingInformationResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=BillingInformationRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=BillingInformationDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     * @Security(name="Bearer")
     */
    #[Route(path: '/{billingInformationId}', name: 'customer_billing_information_update', methods: 'PUT')]
    #[ParamConverter(data: 'billingInformationRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $customerId,
        string $billingInformationId,
        BillingInformationRequest $billingInformationRequest,
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $billingInformation = $this->billingInformationProvider->getByCustomerAndId(
            $customer->getId(),
            Uuid::fromString($billingInformationId)
        );

        $billingInformationRequest->customerId = $customer->getId()->toString();
        $this->billingInformationRequestManager->updateFromRequest($billingInformation, $billingInformationRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->billingInformationResponseMapper->map($billingInformation)
        );
    }
}
