<?php

namespace App\Customer\Controller;

use App\Customer\Request\CustomerLoginGoogleRequest;
use App\Customer\Service\CustomerLoginGoogleManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerGoogleLoginController extends AbstractController
{
    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    private CustomerLoginGoogleManager $customerLoginGoogleManager;

    public function __construct(
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        CustomerLoginGoogleManager $customerLoginGoogleManager
    ) {
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->customerLoginGoogleManager = $customerLoginGoogleManager;
    }

    /**
     * @Route("/customers/login/google", methods="GET", name="customer_google_login_form")
     */
    public function googleLoginForm(): Response
    {
        return $this->render('customer/login_google.html.twig');
    }

    /**
     * @Route("/customers/login/google", methods="POST", name="customer_google_login")
     *
     * @ParamConverter("customerLoginGoogleRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerLoginGoogleRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    public function googleLogin(CustomerLoginGoogleRequest $customerLoginGoogleRequest): Response
    {
        $customer = $this->customerLoginGoogleManager->prepareCustomerFromGoogleToken(
            $customerLoginGoogleRequest->accessToken
        );

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
    }
}
