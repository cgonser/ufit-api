<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\ResponseMapper\CustomerResponseMapper;
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

    public function __construct(
        CustomerProvider $customerProvider,
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->customerResponseMapper = $customerResponseMapper;
        $this->customerProvider = $customerProvider;
    }

    /**
     * @Security(name="Bearer")
     * @OA\Tag(name="Customer")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information all customers",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=CustomerDto::class)))
     *     )*
     * )
     * @Route("/customers", methods="GET", name="customers_get")
     */
    public function getCustomers(): Response
    {
        // TODO: implement authorization
        $customers = $this->customerProvider->findAll();

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->mapMultiple($customers));
    }

    /**
     * @OA\Tag(name="Customer")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer",
     *     @OA\JsonContent(ref=@Model(type=CustomerDto::class))
     * )
     * @Security(name="Bearer")
     * @Route("/customers/{customerId}", methods="GET", name="customers_get_one")
     */
    public function getCustomer(string $customerId): Response
    {
        /** @var Customer $customer */
        $customer = $this->getUser();

        if ('current' !== $customerId && ! $customer->getId()->equals(Uuid::fromString($customerId))) {
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
