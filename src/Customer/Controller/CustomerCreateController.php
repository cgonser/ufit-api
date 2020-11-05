<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Exception\CustomerEmailAddressInUseException;
use App\Customer\Request\CustomerCreateRequest;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Customer\Service\CustomerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerCreateController extends AbstractController
{
    private CustomerService $customerService;

    private CustomerResponseMapper $customerResponseMapper;

    public function __construct(
        CustomerService $customerService,
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->customerService = $customerService;
        $this->customerResponseMapper = $customerResponseMapper;
    }

    /**
     * @Route("/customers", methods="POST", name="customers_create")
     *
     * @ParamConverter("customerCreateRequest", converter="fos_rest.request_body")
     */
    public function create(
        CustomerCreateRequest $customerCreateRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $customer = $this->customerService->create($customerCreateRequest);

            return new ApiJsonResponse(Response::HTTP_CREATED, $this->customerResponseMapper->map($customer));
        } catch (CustomerEmailAddressInUseException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
