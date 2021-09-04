<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
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

#[Route(path: '/customers/{customerId}/measurements')]
class CustomerMeasurementUpdateController extends AbstractController
{
    public function __construct(
        private CustomerMeasurementProvider $customerMeasurementProvider,
        private CustomerMeasurementService $customerMeasurementService,
        private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        private CustomerProvider $customerProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Measurement")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerMeasurementRequest::class)))
     * @OA\Response(response=200, description="Updates a measurement", @OA\JsonContent(ref=@Model(type=CustomerMeasurementDto::class)))
     * @OA\Response(response=400,description="Invalid input")
     * @OA\Response(response=404, description="Measurement not found")
     */
    #[Route(path: '/{customerMeasurementId}', name: 'customers_measurements_update', methods: 'PUT')]
    #[ParamConverter(data: 'customerMeasurementRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,

        ],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $customerId,
        string $customerMeasurementId,
        CustomerMeasurementRequest $customerMeasurementRequest,
    ): Response {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $customerMeasurement = $this->customerMeasurementProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerMeasurementId)
        );

        $this->customerMeasurementService->update($customerMeasurement, $customerMeasurementRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->customerMeasurementResponseMapper->map($customerMeasurement)
        );
    }
}
