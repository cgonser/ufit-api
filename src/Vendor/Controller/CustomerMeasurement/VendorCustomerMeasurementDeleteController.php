<?php

declare(strict_types=1);

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Service\CustomerMeasurementService;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/customers/{customerId}/measurements')]
class VendorCustomerMeasurementDeleteController extends AbstractController
{
    public function __construct(
        private CustomerMeasurementService $customerMeasurementService,
        private CustomerMeasurementProvider $customerMeasurementProvider,
        private SubscriptionCustomerProvider $subscriptionCustomerProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\Response(response=204, description="Deletes a measurement")
     * @OA\Response(response=404, description="Measurement not found")
     */
    #[Route(path: '/{customerMeasurementId}', name: 'vendor_customers_measurements_delete', methods: 'DELETE')]
    public function create(
        string $customerId,
        string $vendorId,
        string $customerMeasurementId
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));
        $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerMeasurementId)
        );

        $this->customerMeasurementService->delete($customerMeasurement);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
