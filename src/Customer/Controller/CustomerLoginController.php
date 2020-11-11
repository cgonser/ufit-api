<?php

namespace App\Customer\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CustomerLoginController extends AbstractController
{
    /**
     * @Route("/customers/login", methods="POST", name="customer_login_check")
     *
     * @OA\Tag(name="Customer")
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
}
