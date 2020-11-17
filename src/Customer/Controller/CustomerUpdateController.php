<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerEmailAddressInUseException;
use App\Customer\Exception\CustomerNotFoundException;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerRequest;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Customer\Service\CustomerService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
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
     * @Route("/customers/{customerId}", methods="PUT", name="customers_update")
     *
     * @ParamConverter("customerRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a customer",
     *     @OA\JsonContent(ref=@Model(type=CustomerDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function update(
        string $customerId,
        CustomerRequest $customerRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $this->customerService->update($customer, $customerRequest);

            return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
        } catch (CustomerNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (CustomerEmailAddressInUseException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
