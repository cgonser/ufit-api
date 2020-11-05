<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerEmailAddressInUseException;
use App\Customer\Exception\CustomerNotFoundException;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerUpdateRequest;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Customer\Service\CustomerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerUpdateController extends AbstractController
{
    private CustomerService $customerService;

    private CustomerResponseMapper $customerResponseMapper;

    private CustomerProvider $customerProvider;

    public function __construct(
        CustomerService $customerService,
        CustomerProvider $customerProvider,
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->customerService = $customerService;
        $this->customerResponseMapper = $customerResponseMapper;
        $this->customerProvider = $customerProvider;
    }

    /**
     * @Route("/customers", methods="PUT", name="customers_update")
     *
     * @ParamConverter("customerUpdateRequest", converter="fos_rest.request_body")
     */
    public function update(
        CustomerUpdateRequest $customerUpdateRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            /** @var Customer $customer */
            $customer = $this->getUser();

            $this->customerService->update($customer, $customerUpdateRequest);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (CustomerNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (CustomerEmailAddressInUseException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
