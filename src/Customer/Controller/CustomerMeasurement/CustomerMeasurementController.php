<?php

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasurementNotFoundException;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
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

    public function __construct(
        CustomerMeasurementProvider $customerMeasurementProvider,
        CustomerMeasurementResponseMapper $customerMeasurementResponseMapper
    ) {
        $this->customerMeasurementResponseMapper = $customerMeasurementResponseMapper;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
    }

    /**
     * @Route("/customers/{customerId}/measurements", methods="GET", name="customers_measurements_get")
     *
     * @OA\Tag(name="CustomerMeasurement")
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
    public function getCustomerMeasurements(string $customerId): Response
    {
        if ('current' == $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $customerMeasurements = $this->customerMeasurementProvider->findByCustomer($customer);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerMeasurementResponseMapper->mapMultiple($customerMeasurements));
    }

    /**
     * @Route("/customers/{customerId}/measurements/{customerMeasurementId}", methods="GET", name="customers_measurements_get_one")
     *
     * @OA\Tag(name="CustomerMeasurement")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a measurement",
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getCustomerMeasurement(string $customerId, string $customerMeasurementId): Response
    {
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId($customer, Uuid::fromString($customerMeasurementId));

            return new ApiJsonResponse(Response::HTTP_OK, $this->customerMeasurementResponseMapper->map($customerMeasurement));
        } catch (CustomerMeasurementNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
