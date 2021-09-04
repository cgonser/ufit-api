<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Core\Dto\JWTAuthenticationTokenDto;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Customer\Request\CustomerCreateRequest;
use App\Customer\Service\CustomerRequestManager;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerCreateController extends AbstractController
{
    public function __construct(
        private CustomerRequestManager $customerRequestManager,
        private AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
    }

    /**
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerCreateRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=JWTAuthenticationTokenDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/customers', name: 'customers_create', methods: 'POST')]
    #[ParamConverter('customerCreateRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function create(
        CustomerCreateRequest $customerCreateRequest,
        Request $request
    ): JWTAuthenticationSuccessResponse {
        $customer = $this->customerRequestManager->createFromRequest($customerCreateRequest, $request->getClientIp());

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
    }
}
