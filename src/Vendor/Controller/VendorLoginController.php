<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorLoginController extends AbstractController
{
    public function __construct(
        private RefreshToken $refreshToken
    ) {
    }

    /**
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
    #[Route(path: '/vendors/login', name: 'vendor_login_check', methods: 'POST')]
    public function login(): void
    {
    }

    /**
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
    #[Route(path: '/vendors/token/refresh', name: 'vendor_token_refresh', methods: 'POST')]
    public function tokenRefresh(Request $request): Response
    {
        return $this->refreshToken->refresh($request);
    }
}
