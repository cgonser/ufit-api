<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerNotFoundException;
use App\Customer\Provider\CustomerProvider;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
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
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
        } catch (CustomerNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
