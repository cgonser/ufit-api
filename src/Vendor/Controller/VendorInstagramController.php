<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Vendor\Exception\VendorFacebookLoginFailedException;
use App\Vendor\Request\VendorInstagramLoginRequest;
use App\Vendor\Service\VendorInstagramLoginService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorInstagramController extends AbstractController
{
    private VendorInstagramLoginService $vendorFacebookLoginService;

    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    public function __construct(
        VendorInstagramLoginService $vendorInstagramLoginService,
        AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
        $this->vendorInstagramLoginService = $vendorInstagramLoginService;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    /**
     * @Route("/vendors/login/instagram", methods="POST", name="vendor_instagram_login")
     *
     * @ParamConverter("vendorInstagramLoginRequest", converter="fos_rest.request_body")
     */
    public function login(VendorInstagramLoginRequest $vendorInstagramLoginRequest): Response
    {
        try {
            $vendor = $this->vendorInstagramLoginService->prepareVendorFromInstagramCode(
                $vendorInstagramLoginRequest->code
            );

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($vendor);
        } catch (VendorFacebookLoginFailedException $e) {
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }
}
