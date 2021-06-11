<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Subscription\Provider\SubscriptionCustomerProvider;
use App\Subscription\Provider\VendorSubscriptionProvider;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    private CustomerResponseMapper $customerResponseMapper;

    private CustomerProvider $customerProvider;

    private SubscriptionCustomerProvider $subscriptionCustomerProvider;

    public function __construct(
        CustomerProvider $customerProvider,
        CustomerResponseMapper $customerResponseMapper,
        SubscriptionCustomerProvider $subscriptionCustomerProvider
    ) {
        $this->customerResponseMapper = $customerResponseMapper;
        $this->customerProvider = $customerProvider;
        $this->subscriptionCustomerProvider = $subscriptionCustomerProvider;
    }

    /**
     * @Route("/customers", methods="GET", name="customers_get")
     *
     * @OA\Tag(name="Customer")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information all customers",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=CustomerDto::class)))
     *     )*
     * )
     * @Security(name="Bearer")
     */
    public function getCustomers(): Response
    {
        // TODO: implement authorization
        $customers = $this->customerProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->customerResponseMapper->mapMultiple($customers)
        );
    }

    /**
     * @Route("/customers/{customerId}", methods="GET", name="customers_get_one")
     *
     * @OA\Tag(name="Customer")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer",
     *     @OA\JsonContent(ref=@Model(type=CustomerDto::class))
     * )
     * @Security(name="Bearer")
     */
    public function getCustomer(string $customerId): Response
    {
        if ($this->getUser() instanceof Customer) {
            /** @var Customer $customer */
            $customer = $this->getUser();

            if ('current' !== $customerId && !$customer->getId()->equals(Uuid::fromString($customerId))) {
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }
        } elseif ($this->getUser() instanceof Vendor) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $customer = $this->subscriptionCustomerProvider->getVendorCustomer($vendor, Uuid::fromString($customerId));
        } else {
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
