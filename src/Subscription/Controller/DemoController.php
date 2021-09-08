<?php

declare(strict_types=1);

namespace App\Subscription\Controller;

use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/demo/subscription')]
class DemoController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorPlanProvider $vendorPlanProvider,
    ) {
    }

    #[Route(name: 'demo_subscription', methods: 'GET')]
    public function demo(): Response
    {
        return $this->render(
            'demo/subscription/subscription.html.twig',
            [
                'vendors' => $this->vendorProvider->findAll()
//                'vendorPlans' => $this->vendorPlanProvider->findVendorPlans()
            ]
        );
    }
}
