<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\Exception\CustomerEmailAddressInUseException;
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
     * @ParamConverter("customerRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new customer",
     *     @OA\JsonContent(ref=@Model(type=CustomerDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        CustomerRequest $customerRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $customer = $this->customerService->create($customerRequest);

            return new ApiJsonResponse(Response::HTTP_CREATED, $this->customerResponseMapper->map($customer));
        } catch (CustomerEmailAddressInUseException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
