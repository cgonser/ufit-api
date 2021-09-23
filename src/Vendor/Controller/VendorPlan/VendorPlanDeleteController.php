<?php

declare(strict_types=1);

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorPlanManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/plans')]
class VendorPlanDeleteController extends AbstractController
{
    public function __construct(
        private VendorPlanManager $vendorPlanManager,
        private VendorPlanProvider $vendorPlanProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Plan")
     * @OA\Response(response=204, description="Deletes a plan")
     * @OA\Response(response=404, description="Plan not found")
     */
    #[Route(path: '/{vendorPlanId}', name: 'vendors_plans_delete', methods: 'DELETE')]
    public function create(string $vendorId, string $vendorPlanId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));
        $this->vendorPlanManager->delete($vendorPlan);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
