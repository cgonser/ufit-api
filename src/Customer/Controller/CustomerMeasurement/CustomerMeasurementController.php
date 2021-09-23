<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasurementNotFoundException;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/customers/{customerId}/measurements')]
class CustomerMeasurementController extends AbstractController
{
    public function __construct(
        private CustomerMeasurementProvider $customerMeasurementProvider,
        private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        private CustomerProvider $customerProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Measurement")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer measurements",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CustomerMeasurementDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(name: 'customers_measurements_get', methods: 'GET')]
    public function getCustomerMeasurements(string $customerId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $customerMeasurements = $this->customerMeasurementProvider->findByCustomer($customer);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->customerMeasurementResponseMapper->mapMultiple($customerMeasurements)
        );
    }

    /**
     * @OA\Tag(name="Customer / Measurement")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/{customerMeasurementId}', name: 'customers_measurements_get_one', methods: 'GET')]
    public function getCustomerMeasurement(string $customerId, string $customerMeasurementId): Response
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

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
