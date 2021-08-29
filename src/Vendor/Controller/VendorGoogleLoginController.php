<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Vendor\Request\VendorLoginGoogleRequest;
use App\Vendor\Service\VendorLoginGoogleManager;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorGoogleLoginController extends AbstractController
{
    public function __construct(
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
        private VendorLoginGoogleManager $vendorLoginGoogleManager
    ) {
    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorLoginGoogleRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    #[Route(path: '/vendors/login/google', methods: 'POST', name: 'vendor_google_login')]
    #[ParamConverter(data: 'vendorLoginGoogleRequest', converter: 'fos_rest.request_body', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ])]
    public function googleLogin(VendorLoginGoogleRequest $vendorLoginGoogleRequest): JWTAuthenticationSuccessResponse
    {
        $vendor = $this->vendorLoginGoogleManager->prepareVendorFromGoogleToken(
            $vendorLoginGoogleRequest->accessToken
        );

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($vendor);
    }

    /**
     * @OA\Tag(name="Vendor / Demo")
     */
    #[Route(path: '/vendors/login/google', methods: 'GET', name: 'vendor_google_login_form')]
    public function googleLoginForm(): Response
    {
        return $this->render('vendor/login_google.html.twig');
    }
}
