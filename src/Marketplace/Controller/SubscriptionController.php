<?php

namespace App\Marketplace\Controller;

use App\Marketplace\Service\Subscription\SubscriptionManager;
use App\Vendor\Repository\VendorPlanRepository;
use App\Vendor\Repository\VendorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
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
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/marketplace/{vendorSlug}/subscribe/{vendorPlanId}", name="marketplace_subscribe")
     */
    public function subscribe(string $vendorSlug, int $vendorPlanId)
    {
        $vendorPlan = $this->vendorPlanRepository->find($vendorPlanId);

        $this->subscriptionManager->createSubscription($this->getUser(), $vendorPlan);

        return $this->redirectToRoute('customer_dashboard');
    }
}