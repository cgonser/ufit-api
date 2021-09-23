<?php

declare(strict_types=1);

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/customers/{customerId}/measurements')]
class VendorCustomerMeasurementController extends AbstractController
{
    public function __construct(
        private CustomerMeasurementProvider $customerMeasurementProvider,
        private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        private SubscriptionCustomerProvider $subscriptionCustomerProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CustomerMeasurementDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(name: 'vendor_customers_measurements_get', methods: 'GET')]
    public function getVendorCustomerMeasurements(string $vendorId, string $customerId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));
        $customerMeasurements = $this->customerMeasurementProvider->findByCustomer($customer);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->customerMeasurementResponseMapper->mapMultiple($customerMeasurements)
        );
    }

    /**
     * @OA\Tag(name="Vendor / Customer / Measurement")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/{customerMeasurementId}', name: 'vendor_customers_measurements_get_one', methods: 'GET')]
    public function getCustomerMeasurement(
        string $vendorId,
        string $customerId,
        string $customerMeasurementId
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

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
