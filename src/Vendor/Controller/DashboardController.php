<?php

namespace App\Vendor\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_VENDOR")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="vendor_dashboard")
     */
    public function index()
    {
        return $this->render('vendor/dashboard/index.html.twig');
    }
}
