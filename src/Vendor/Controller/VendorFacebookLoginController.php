<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Vendor\Exception\VendorFacebookLoginFailedException;
use App\Vendor\Request\VendorLoginFacebookRequest;
use App\Vendor\Service\VendorFacebookLoginService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorFacebookLoginController extends AbstractController
{
    /**
     * @var VendorFacebookLoginService
     */
    private VendorFacebookLoginService $vendorFacebookLoginService;

    public function __construct(VendorFacebookLoginService $vendorFacebookLoginService)
    {
        $this->vendorFacebookLoginService = $vendorFacebookLoginService;
    }

    /**
     * @Route("/vendors/login/facebook", methods="POST", name="vendor_facebook_login")
     *
     * @ParamConverter("vendorLoginFacebookRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=VendorLoginFacebookRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the API access token",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Invalid credentials"
     * )
     */
    public function facebookLogin(VendorLoginFacebookRequest $vendorLoginFacebookRequest): Response
    {
        try {
            $accessToken = $this->vendorFacebookLoginService->authenticateVendorFromToken(
                $vendorLoginFacebookRequest->accessToken
            );

            return new JsonResponse(['token' => $accessToken]);
        } catch (VendorFacebookLoginFailedException $e) {
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }
}
