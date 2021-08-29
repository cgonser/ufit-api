<?php

declare(strict_types=1);

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Request\CustomerMeasurementRequest;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use App\Customer\Service\CustomerMeasurementService;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/vendors/{vendorId}/customers/{customerId}/measurements')]
class VendorCustomerMeasurementCreateController extends AbstractController
{
    public function __construct(
        private CustomerMeasurementService $customerMeasurementService,
        private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        private SubscriptionCustomerProvider $subscriptionCustomerProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerMeasurementRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(name: 'vendor_customers_measurements_create', methods: 'POST')]
    #[ParamConverter(data: 'customerMeasurementRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function create(
        string $customerId,
        string $vendorId,
        CustomerMeasurementRequest $customerMeasurementRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }

        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));
        $customerMeasurement = $this->customerMeasurementService->create($customer, $customerMeasurementRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->customerMeasurementResponseMapper->map($customerMeasurement)
        );
    }
}
