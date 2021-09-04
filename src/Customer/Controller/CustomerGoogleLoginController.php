<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Request\CustomerLoginGoogleRequest;
use App\Customer\Service\CustomerLoginGoogleManager;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerGoogleLoginController extends AbstractController
{
    public function __construct(
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
        private CustomerLoginGoogleManager $customerLoginGoogleManager
    ) {
    }

    /**
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerLoginGoogleRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    #[Route(path: '/customers/login/google', name: 'customer_google_login', methods: 'POST')]
    #[ParamConverter(data: 'customerLoginGoogleRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function googleLogin(
        CustomerLoginGoogleRequest $customerLoginGoogleRequest
    ): JWTAuthenticationSuccessResponse {
        $customer = $this->customerLoginGoogleManager->prepareCustomerFromGoogleToken(
            $customerLoginGoogleRequest->accessToken
        );

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
    }

    /**
     * @OA\Tag(name="Customer / Demo")
     */
    #[Route(path: '/customers/login/google', name: 'customer_google_login_form', methods: 'GET')]
    public function googleLoginForm(): Response
    {
        return $this->render('customer/login_google.html.twig');
    }
}
