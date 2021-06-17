<?php

namespace App\Vendor\Controller\Customer;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Request\VendorCustomerRequest;
use App\Vendor\Service\VendorCustomerRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerUpdateController extends AbstractController
{
    private CustomerResponseMapper $customerResponseMapper;
    private VendorCustomerRequestManager $customerRequestManager;
    private SubscriptionCustomerProvider $subscriptionCustomerProvider;

    public function __construct(
        VendorCustomerRequestManager $customerRequestManager,
        CustomerResponseMapper $customerResponseMapper,
        SubscriptionCustomerProvider $subscriptionCustomerProvider
    ) {
        $this->customerRequestManager = $customerRequestManager;
        $this->customerResponseMapper = $customerResponseMapper;
        $this->subscriptionCustomerProvider = $subscriptionCustomerProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/customers/{customerId}", methods="PUT", name="vendor_customers_update")
     * @ParamConverter("customerRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor / Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorCustomerRequest::class)))
     * @OA\Response(response=200, description="Updates a customer", @OA\JsonContent(ref=@Model(type=CustomerDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function update(
        string $customerId,
        VendorCustomerRequest $customerRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        /** @var Vendor $vendor */
        $vendor = $this->getUser();

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));

        $this->customerRequestManager->updateFromRequest($customer, $customerRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
