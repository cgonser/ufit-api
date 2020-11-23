<?php

namespace App\Vendor\Controller;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VendorLoginController extends AbstractController
{
    private RefreshToken $refreshTokenService;

    public function __construct(RefreshToken $refreshTokenService)
    {
        $this->refreshTokenService = $refreshTokenService;
    }

    /**
     * @Route("/vendors/login", methods="POST", name="vendor_login_check")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         required={"username", "password"},
     *         @OA\Property(property="username", type="string"),
     *         @OA\Property(property="password", type="string")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Provides the authentication token"
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
    public function login()
    {
    }

    /**
     * @Route("/vendors/token/refresh", methods="POST", name="vendor_token_refresh")
     *
     * @OA\Tag(name="Vendor")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="refresh_token",
     *                 type="string",
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Provides the refreshed token"
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
    public function tokenRefresh(Request $request)
    {
        return $this->refreshTokenService->refresh($request);
    }
}
