<?php

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasurementInvalidTakenAtException;
use App\Customer\Request\CustomerMeasurementRequest;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use App\Customer\Service\CustomerMeasurementService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerMeasurementCreateController extends AbstractController
{
    private CustomerMeasurementService $customerMeasurementService;

    private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper;

    public function __construct(
        CustomerMeasurementService $customerMeasurementService,
        CustomerMeasurementResponseMapper $customerMeasurementResponseMapper
    ) {
        $this->customerMeasurementResponseMapper = $customerMeasurementResponseMapper;
        $this->customerMeasurementService = $customerMeasurementService;
    }

    /**
     * @Route("/customers/{customerId}/measurements", methods="POST", name="customers_measurements_create")
     *
     * @ParamConverter("customerMeasurementRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="CustomerMeasurement")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasurementRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new measurement",
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        string $customerId,
        CustomerMeasurementRequest $customerMeasurementRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerMeasurement = $this->customerMeasurementService->create($customer, $customerMeasurementRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->customerMeasurementResponseMapper->map($customerMeasurement)
            );
        } catch (CustomerMeasurementInvalidTakenAtException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
