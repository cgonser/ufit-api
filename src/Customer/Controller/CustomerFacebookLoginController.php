<?php

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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerFacebookLoginController extends AbstractController
{
    private CustomerFacebookLoginManager $customerFacebookLoginManager;

    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    public function __construct(
        CustomerFacebookLoginManager $customerFacebookLoginManager,
        AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
        $this->customerFacebookLoginManager = $customerFacebookLoginManager;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    /**
     * @Route("/customers/login/facebook", methods="POST", name="customer_facebook_login")
     * @ParamConverter("customerFacebookLoginRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerFacebookLoginRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    public function facebookLogin(CustomerFacebookLoginRequest $customerFacebookLoginRequest): Response
    {
        try {
            $customer = $this->customerFacebookLoginManager->prepareCustomerFromFacebookToken(
                $customerFacebookLoginRequest->accessToken
            );

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
        } catch (CustomerFacebookLoginFailedException $e) {
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }

    /**
     * @Route("/customers/login/facebook", methods="GET", name="customer_facebook_login_button")
     *
     * @OA\Tag(name="Customer / Demo")
     */
    public function facebookLoginButton(): Response
    {
        return $this->render('customer/facebook_login.html.twig');
    }
}
