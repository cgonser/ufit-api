<?php

namespace App\Vendor\Controller;

use App\Vendor\Request\VendorLoginGoogleRequest;
use App\Vendor\Service\VendorLoginGoogleManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorGoogleLoginController extends AbstractController
{
    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    private VendorLoginGoogleManager $vendorLoginGoogleManager;

    public function __construct(
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        vendorLoginGoogleManager $vendorLoginGoogleManager
    ) {
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->vendorLoginGoogleManager = $vendorLoginGoogleManager;
    }

    /**
     * @Route("/vendors/login/google", methods="GET", name="vendor_google_login_form")
     */
    public function googleLoginForm(): Response
    {
        return $this->render('vendor/login_google.html.twig');
    }

    /**
     * @Route("/vendors/login/google", methods="POST", name="vendor_google_login")
     *
     * @ParamConverter("vendorLoginGoogleRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorLoginGoogleRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    public function googleLogin(VendorLoginGoogleRequest $vendorLoginGoogleRequest): Response
    {
        $vendor = $this->vendorLoginGoogleManager->prepareVendorFromGoogleToken(
            $vendorLoginGoogleRequest->accessToken
        );

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($vendor);
    }
}
