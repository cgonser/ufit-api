<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerNotFoundException;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    private CustomerResponseMapper $customerResponseMapper;

    public function __construct(
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->customerResponseMapper = $customerResponseMapper;
    }

    /**
     * @Route("/customers", methods="GET", name="customers_get")
     */
    public function getCustomer(): Response
    {
        try {
            /** @var Customer $customer */
            $customer = $this->getUser();

            return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
        } catch (CustomerNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
