<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
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

#[Route(path: '/customers')]
class CustomerController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerResponseMapper $customerResponseMapper
    ) {
    }

//    /**
//     * @Security(name="Bearer")
//     * @OA\Tag(name="Customer")
//     * @OA\Response(
//     *     response=200,
//     *     description="Returns the information all customers",
//     *     @OA\JsonContent(
//     *         type="array",
//     *         @OA\Items(ref=@Model(type=CustomerDto::class)))
//     *     )*
//     * )
//     */
//    #[Route(name: 'customers_get', methods: 'GET')]
//    public function getCustomers(): ApiJsonResponse
//    {
//        $customers = $this->customerProvider->findAll();
//
//        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->mapMultiple($customers));
//    }

    /**
     * @OA\Tag(name="Customer")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer",
     *     @OA\JsonContent(ref=@Model(type=CustomerDto::class))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/{customerId}', name: 'customers_get_one', methods: 'GET')]
    public function getCustomer(string $customerId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
