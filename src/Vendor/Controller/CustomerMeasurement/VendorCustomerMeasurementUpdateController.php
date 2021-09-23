<?php

declare(strict_types=1);

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Request\CustomerMeasurementRequest;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use App\Customer\Service\CustomerMeasurementService;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/customers/{customerId}/measurements')]
class VendorCustomerMeasurementUpdateController extends AbstractController
{
    public function __construct(
        private CustomerMeasurementProvider $customerMeasurementProvider,
        private CustomerMeasurementService $customerMeasurementService,
        private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        private SubscriptionCustomerProvider $subscriptionCustomerProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerMeasurementRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Measurement not found")
     */
    #[Route(path: '/{customerMeasurementId}', name: 'vendor_customers_measurements_update', methods: 'PUT')]
    #[ParamConverter(
        data: 'customerMeasurementRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        string $customerId,
        string $vendorId,
        string $customerMeasurementId,
        CustomerMeasurementRequest $customerMeasurementRequest,
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));
        $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerMeasurementId)
        );
        $this->customerMeasurementService->update($customerMeasurement, $customerMeasurementRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->customerMeasurementResponseMapper->map($customerMeasurement)
        );
    }
}
