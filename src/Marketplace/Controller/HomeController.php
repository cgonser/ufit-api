<?php

namespace App\Marketplace\Controller;

use App\Vendor\Repository\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private VendorRepository $vendorRepository;

    public function __construct(
        VendorRepository $vendorRepository
    )
    {
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $vendors = $this->vendorRepository->findAll();

        return $this->render('marketplace/homepage.html.twig', [
            'vendors' => $vendors,
        ]);
    }
}
