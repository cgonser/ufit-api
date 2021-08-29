<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Vendor\Exception\VendorFacebookLoginFailedException;
use App\Vendor\Request\VendorLoginFacebookRequest;
use App\Vendor\Service\VendorFacebookLoginService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorFacebookLoginController extends AbstractController
{
    public function __construct(
        private VendorFacebookLoginService $vendorFacebookLoginService,
        private AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorLoginFacebookRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    #[Route(path: '/vendors/login/facebook', name: 'vendor_facebook_login', methods: 'POST')]
    #[ParamConverter(data: 'vendorLoginFacebookRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function facebookLogin(VendorLoginFacebookRequest $vendorLoginFacebookRequest, Request $request): Response
    {
        try {
            $vendor = $this->vendorFacebookLoginService->prepareVendorFromFacebookToken(
                $vendorLoginFacebookRequest->accessToken,
                $request->getClientIp()
            );

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($vendor);
        } catch (VendorFacebookLoginFailedException $vendorFacebookLoginFailedException) {
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED, $vendorFacebookLoginFailedException->getMessage());
        }
    }
}
