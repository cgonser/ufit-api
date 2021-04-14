<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\Exception\CustomerEmailAddressInUseException;
use App\Customer\Request\CustomerRequest;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Customer\Service\CustomerRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerCreateController extends AbstractController
{
    private CustomerRequestManager $customerRequestManager;

    private CustomerResponseMapper $customerResponseMapper;

    public function __construct(
        CustomerRequestManager $customerRequestManager,
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->customerRequestManager = $customerRequestManager;
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
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $customer = $this->customerRequestManager->createFromRequest($customerRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->customerResponseMapper->map($customer));
    }
}
