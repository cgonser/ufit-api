<?php

namespace App\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractController
{
    /**
     * @Route("/health/check", methods="GET", name="health_check")
     **/
    public function healthCheck(): Response
    {
        return new Response(null, Response::HTTP_OK);
    }
}