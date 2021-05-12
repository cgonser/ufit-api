<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Dto\JWTAuthenticationTokenDto;
use App\Customer\Request\CustomerRequest;
use App\Customer\Service\CustomerRequestManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerCreateController extends AbstractController
{
    private CustomerRequestManager $customerRequestManager;

    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    public function __construct(
        CustomerRequestManager $customerRequestManager,
        AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
        $this->customerRequestManager = $customerRequestManager;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    /**
     * @Route("/customers", methods="POST", name="customers_create")
     * @ParamConverter("customerRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=JWTAuthenticationTokenDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function create(
        CustomerRequest $customerRequest,
        ConstraintViolationListInterface $validationErrors,
        Request $request
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $customer = $this->customerRequestManager->createFromRequest($customerRequest, $request->getClientIp());

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
    }
}
