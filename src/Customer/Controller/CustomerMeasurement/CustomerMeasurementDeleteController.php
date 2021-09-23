<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Service\CustomerMeasurementService;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/customers/{customerId}/measurements')]
class CustomerMeasurementDeleteController extends AbstractController
{
    public function __construct(
        private CustomerMeasurementService $customerMeasurementService,
        private CustomerMeasurementProvider $customerMeasurementProvider,
        private CustomerProvider $customerProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Measurement")
     * @OA\Response(response=204, description="Deletes a measurement")
     * @OA\Response(response=404, description="Measurement not found")
     */
    #[Route(path: '/{customerMeasurementId}', name: 'customers_measurements_delete', methods: 'DELETE')]
    public function create(string $customerId, string $customerMeasurementId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerMeasurementId)
        );

        $this->customerMeasurementService->delete($customerMeasurement);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
