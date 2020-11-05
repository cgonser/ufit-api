<?php

namespace App\Marketplace\Controller;

use App\Marketplace\Service\Subscription\SubscriptionManager;
use App\Vendor\Entity\Vendor;
use App\Vendor\Repository\VendorPlanRepository;
use App\Vendor\Repository\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VendorController extends AbstractController
{
    private VendorRepository $vendorRepository;

    private VendorPlanRepository $vendorPlanRepository;

    private SubscriptionManager $subscriptionManager;

    public function __construct(
        VendorRepository $vendorRepository,
        VendorPlanRepository $vendorPlanRepository,
        SubscriptionManager $subscriptionManager
    )
    {
        $this->vendorRepository = $vendorRepository;
        $this->vendorPlanRepository = $vendorPlanRepository;
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * @Route("/vendors", name="marketplace_vendor_list")
     */
    public function index()
    {
        $vendors = $this->vendorRepository->findAll();

        return $this->render('marketplace/vendor/index.html.twig', [
            'vendors' => $vendors,
        ]);
    }

    /**
     * @Route("/vendor/{vendorSlug}", name="marketplace_vendor_show")
     */
    public function show(string $vendorSlug)
    {
        /** @var Vendor $vendor */
        $vendor = $this->vendorRepository->findOneBy([
            'slug' => $vendorSlug,
        ]);

        $vendorPlans = $this->vendorPlanRepository->findActivePlansByVendor($vendor);

        return $this->render('marketplace/vendor/show.html.twig', [
            'vendor' => $vendor,
            'vendor_plans' => $vendorPlans,
        ]);
    }
}
