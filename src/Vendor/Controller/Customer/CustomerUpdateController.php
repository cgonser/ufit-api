<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Customer;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerDto;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorCustomerRequest;
use App\Vendor\Service\VendorCustomerRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerUpdateController extends AbstractController
{
    public function __construct(
        private VendorCustomerRequestManager $vendorCustomerRequestManager,
        private CustomerResponseMapper $customerResponseMapper,
        private SubscriptionCustomerProvider $subscriptionCustomerProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorCustomerRequest::class)))
     * @OA\Response(response=200, description="Updates a customer", @OA\JsonContent(ref=@Model(type=CustomerDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/vendors/{vendorId}/customers/{customerId}', name: 'vendor_customers_update', methods: 'PUT')]
    #[ParamConverter(
        data: 'customerRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        string $customerId,
        string $vendorId,
        VendorCustomerRequest $vendorCustomerRequest,
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));

        $this->vendorCustomerRequestManager->updateFromRequest($customer, $vendorCustomerRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
