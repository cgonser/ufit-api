<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Customer\Exception\CustomerFacebookLoginFailedException;
use App\Customer\Request\CustomerFacebookLoginRequest;
use App\Customer\Service\CustomerFacebookLoginManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerFacebookLoginController extends AbstractController
{
    public function __construct(
        private CustomerFacebookLoginManager $customerFacebookLoginManager,
        private AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
    }

    /**
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerFacebookLoginRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    #[Route(path: '/customers/login/facebook', name: 'customer_facebook_login', methods: 'POST')]
    #[ParamConverter(
        data: 'customerFacebookLoginRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function facebookLogin(
        CustomerFacebookLoginRequest $customerFacebookLoginRequest,
        Request $request
    ): Response {
        $customer = $this->customerFacebookLoginManager->prepareCustomerFromFacebookToken(
            $customerFacebookLoginRequest->accessToken,
            $request->getClientIp()
        );

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
    }

    /**
     * @OA\Tag(name="Demo / Customer")
     */
    #[Route(path: '/demo/customers/login/facebook', name: 'customer_facebook_login_button', methods: 'GET')]
    public function facebookLoginButton(): Response
    {
        return $this->render('demo/customer/facebook_login.html.twig');
    }
}
