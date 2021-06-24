<?php

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Service\CustomerMeasurementService;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerMeasurementDeleteController extends AbstractController
{
    private CustomerMeasurementService $customerMeasurementService;

    private CustomerMeasurementProvider $customerMeasurementProvider;

    private CustomerProvider $customerProvider;

    public function __construct(
        CustomerMeasurementService $customerMeasurementService,
        CustomerMeasurementProvider $customerMeasurementProvider,
        CustomerProvider $customerProvider
    ) {
        $this->customerMeasurementService = $customerMeasurementService;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
        $this->customerProvider = $customerProvider;
    }

    /**
     * @Route("/customers/{customerId}/measurements/{customerMeasurementId}", methods="DELETE", name="customers_measurements_delete")
     *
     * @OA\Tag(name="Customer / Measurement")
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
        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            if ($this->getUser() instanceof Customer) {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            // TODO: implement proper vendor authorization
            $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        }

        $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerMeasurementId)
        );

        $this->customerMeasurementService->delete($customerMeasurement);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
