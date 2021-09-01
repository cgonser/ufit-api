<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
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
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerRequestManager $customerRequestManager,
        private CustomerResponseMapper $customerResponseMapper,
    ) {
    }

    /**
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerRequest::class)))
     * @OA\Response(response=200, description="Updates a customer", @OA\JsonContent(ref=@Model(type=CustomerDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/customers/{customerId}', name: 'customers_update', methods: 'PUT')]
    #[ParamConverter(data: 'customerRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $customerId,
        CustomerRequest $customerRequest,
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $this->customerRequestManager->updateFromRequest($customer, $customerRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerResponseMapper->map($customer));
    }
}
