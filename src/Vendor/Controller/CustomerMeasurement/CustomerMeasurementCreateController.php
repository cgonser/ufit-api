<?php

namespace App\Vendor\Controller\CustomerMeasurement;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasurementDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasurementInvalidTakenAtException;
use App\Customer\Provider\CustomerProvider;
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

class CustomerMeasurementCreateController extends AbstractController
{
    private CustomerMeasurementService $customerMeasurementService;
    private CustomerMeasurementResponseMapper $customerMeasurementResponseMapper;
    private CustomerProvider $customerProvider;
    private SubscriptionCustomerProvider $subscriptionCustomerProvider;

    public function __construct(
        CustomerMeasurementService $customerMeasurementService,
        CustomerMeasurementResponseMapper $customerMeasurementResponseMapper,
        CustomerProvider $customerProvider,
        SubscriptionCustomerProvider $subscriptionCustomerProvider
    ) {
        $this->customerMeasurementResponseMapper = $customerMeasurementResponseMapper;
        $this->customerMeasurementService = $customerMeasurementService;
        $this->customerProvider = $customerProvider;
        $this->subscriptionCustomerProvider = $subscriptionCustomerProvider;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/customers/{customerId}/measurements",
     *     methods="POST",
     *     name="vendor_customers_measurements_create"
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
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        /** @var Vendor $vendor */
        $vendor = $this->getUser();
        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));
        $customerMeasurement = $this->customerMeasurementService->create($customer, $customerMeasurementRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->customerMeasurementResponseMapper->map($customerMeasurement)
        );
    }
}
