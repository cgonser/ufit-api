<?php

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Request\CustomerMeasurementRequest;
use App\Customer\ResponseMapper\CustomerMeasurementResponseMapper;
use App\Customer\Service\CustomerMeasurementService;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Entity\Vendor;
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
    private SubscriptionCustomerProvider $subscriptionCustomerProvider;

    public function __construct(
        CustomerMeasurementProvider $customerMeasurementProvider,
        CustomerMeasurementService $customerMeasurementService,
        CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        SubscriptionCustomerProvider $subscriptionCustomerProvider
    ) {
        $this->customerMeasurementResponseMapper = $customerMeasurementResponseMapper;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
        $this->customerMeasurementService = $customerMeasurementService;
        $this->subscriptionCustomerProvider = $subscriptionCustomerProvider;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/customers/{customerId}/measurements/{customerMeasurementId}",
     *     methods="PUT",
     *     name="vendor_customers_measurements_update"
     * )
     *
     * @ParamConverter("customerMeasurementRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor / Customer / Measurement")
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
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        /** @var Vendor $vendor */
        $vendor = $this->getUser();
        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));

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
