<?php

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Response\ApiJsonResponse;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Service\CustomerMeasurementService;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Entity\Vendor;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerMeasurementDeleteController extends AbstractController
{
    private CustomerMeasurementService $customerMeasurementService;
    private CustomerMeasurementProvider $customerMeasurementProvider;
    private SubscriptionCustomerProvider $subscriptionCustomerProvider;

    public function __construct(
        CustomerMeasurementService $customerMeasurementService,
        CustomerMeasurementProvider $customerMeasurementProvider,
        SubscriptionCustomerProvider $subscriptionCustomerProvider
    ) {
        $this->customerMeasurementService = $customerMeasurementService;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
        $this->subscriptionCustomerProvider = $subscriptionCustomerProvider;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/customers/{customerId}/measurements/{customerMeasurementId}",
     *     methods="DELETE",
     *     name="customers_measurements_delete"
     * )
     *
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a measurement"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Measurement not found"
     * )
     */
    public function create(
        string $customerId,
        string $customerMeasurementId
    ): Response {
        /** @var Vendor $vendor */
        $vendor = $this->getUser();
        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));

        $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerMeasurementId)
        );

        $this->customerMeasurementService->delete($customerMeasurement);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
