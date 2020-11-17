<?php

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasurementInvalidDurationException;
use App\Customer\Exception\CustomerMeasurementNotFoundException;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Request\CustomerMeasurementUpdateRequest;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use App\Customer\Service\CustomerMeasurementService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerMeasurementDeleteController extends AbstractController
{
    private CustomerMeasurementService $customerMeasurementService;

    private CustomerMeasurementProvider $customerMeasurementProvider;

    public function __construct(
        CustomerMeasurementService $customerMeasurementService,
        CustomerMeasurementProvider $customerMeasurementProvider
    ) {
        $this->customerMeasurementService = $customerMeasurementService;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
    }

    /**
     * @Route("/customers/{customerId}/measurements/{customerMeasurementId}", methods="DELETE", name="customers_measurements_delete")
     *
     * @OA\Tag(name="CustomerMeasurement")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a measurement"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Plan not found"
     * )
     */
    public function create(
        string $customerId,
        string $customerMeasurementId
    ): Response {
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId($customer, Uuid::fromString($customerMeasurementId));

            $this->customerMeasurementService->delete($customerMeasurement);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (CustomerMeasurementNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
