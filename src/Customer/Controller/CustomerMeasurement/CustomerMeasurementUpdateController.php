<?php

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasurementInvalidTakenAtException;
use App\Customer\Exception\CustomerMeasurementNotFoundException;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerMeasurementRequest;
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

class CustomerMeasurementUpdateController extends AbstractController
{
    private CustomerMeasurementProvider $customerMeasurementProvider;

    private CustomerMeasurementService $customerMeasurementService;

    private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper;

    private CustomerProvider $customerProvider;

    public function __construct(
        CustomerMeasurementProvider $customerMeasurementProvider,
        CustomerMeasurementService $customerMeasurementService,
        CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        CustomerProvider $customerProvider
    ) {
        $this->customerMeasurementResponseMapper = $customerMeasurementResponseMapper;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
        $this->customerMeasurementService = $customerMeasurementService;
        $this->customerProvider = $customerProvider;
    }

    /**
     * @Route("/customers/{customerId}/measurements/{customerMeasurementId}", methods="PUT", name="customers_measurements_update")
     *
     * @ParamConverter("customerMeasurementRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="CustomerMeasurement")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasurementRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a measurement",
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Measurement not found"
     * )
     */
    public function update(
        string $customerId,
        string $customerMeasurementId,
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

            $this->customerMeasurementService->update($customerMeasurement, $customerMeasurementRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->customerMeasurementResponseMapper->map($customerMeasurement)
            );
        } catch (CustomerMeasurementNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (CustomerMeasurementInvalidTakenAtException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
