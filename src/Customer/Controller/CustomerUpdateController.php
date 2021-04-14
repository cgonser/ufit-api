<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerRequest;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Customer\Service\CustomerRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerUpdateController extends AbstractController
{
    private CustomerRequestManager $customerRequestManager;

    private CustomerResponseMapper $customerResponseMapper;

    private CustomerProvider $customerProvider;

    public function __construct(
        CustomerRequestManager $customerRequestManager,
        CustomerProvider $customerProvider,
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->customerRequestManager = $customerRequestManager;
        $this->customerResponseMapper = $customerResponseMapper;
        $this->customerProvider = $customerProvider;
    }

    /**
     * @Route("/customers/{customerId}", methods="PUT", name="customers_update")
     * @ParamConverter("customerRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerRequest::class)))
     * @OA\Response(response=200, description="Updates a customer", @OA\JsonContent(ref=@Model(type=CustomerDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function update(
        string $customerId,
        CustomerRequest $customerRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            if ($this->getUser() instanceof Customer) {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            // TODO: implement proper vendor authorization
            $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        }

        $this->customerRequestManager->updateFromRequest($customer, $customerRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
