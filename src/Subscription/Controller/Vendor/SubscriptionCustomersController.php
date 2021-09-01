<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerDto;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Request\SubscriptionSearchRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionCustomersController extends AbstractController
{
    public function __construct(
        private SubscriptionProvider $subscriptionProvider,
        private SubscriptionResponseMapper $subscriptionResponseMapper,
        private SubscriptionCustomerProvider $subscriptionCustomerProvider,
        private CustomerResponseMapper $customerResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Customer")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SubscriptionSearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CustomerDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/vendors/{vendorId}/customers', name: 'vendors_subscriptions_get_customers', methods: 'GET')]
    #[ParamConverter(data: 'subscriptionSearchRequest', converter: 'querystring')]
    public function getCustomers(
        string $vendorId,
        SubscriptionSearchRequest $subscriptionSearchRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $subscriptionSearchRequest->vendorId = $vendor->getId()->toString();
        $customers = $this->subscriptionProvider->searchCustomers($subscriptionSearchRequest);
        $count = $this->subscriptionProvider->countCustomers($subscriptionSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->subscriptionResponseMapper->mapMultipleCustomers($customers),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Vendor / Customer")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CustomerDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/vendors/{vendorId}/customers/{customerId}',
        name: 'vendors_subscriptions_get_one_customer',
        methods: 'GET'
    )]
    public function getCustomer(string $vendorId, string $customerId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
