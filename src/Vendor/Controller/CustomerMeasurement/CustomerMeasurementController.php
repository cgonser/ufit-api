<?php

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasurementNotFoundException;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerMeasurementController extends AbstractController
{
    private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper;
    private CustomerMeasurementProvider $customerMeasurementProvider;
    private CustomerProvider $customerProvider;
    private SubscriptionCustomerProvider $subscriptionCustomerProvider;

    public function __construct(
        CustomerMeasurementProvider $customerMeasurementProvider,
        CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        CustomerProvider $customerProvider,
        SubscriptionCustomerProvider $subscriptionCustomerProvider
    ) {
        $this->customerMeasurementResponseMapper = $customerMeasurementResponseMapper;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
        $this->customerProvider = $customerProvider;
        $this->subscriptionCustomerProvider = $subscriptionCustomerProvider;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/customers/{customerId}/measurements",
     *     methods="GET",
     *     name="vendor_customers_measurements_get"
     * )
     *
     *
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer measurements",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=CustomerMeasurementDto::class)))
     *     )
     * )
     *
     * @Security(name="Bearer")
     */
    public function getCustomerMeasurements(string $vendorId, string $customerId): Response
    {
        /** @var Vendor $vendor */
        $vendor = $this->getUser();

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));

        $customerMeasurements = $this->customerMeasurementProvider->findByCustomer($customer);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->customerMeasurementResponseMapper->mapMultiple($customerMeasurements)
        );
    }

    /**
     * @Route("/customers/{customerId}/measurements/{customerMeasurementId}", methods="GET", name="vendor_customers_measurements_get_one")
     *
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a measurement",
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getCustomerMeasurement(
        string $vendorId,
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

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->customerMeasurementResponseMapper->map($customerMeasurement)
        );
    }
}
