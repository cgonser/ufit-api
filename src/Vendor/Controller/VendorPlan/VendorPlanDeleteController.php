<?php

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Service\VendorPlanService;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorPlanDeleteController extends AbstractController
{
    private VendorPlanService $vendorPlanService;

    private VendorPlanProvider $vendorPlanProvider;

    public function __construct(
        VendorPlanService $vendorPlanService,
        VendorPlanProvider $vendorPlanProvider
    ) {
        $this->vendorPlanService = $vendorPlanService;
        $this->vendorPlanProvider = $vendorPlanProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/plans/{vendorPlanId}", methods="DELETE", name="vendors_plans_delete")
     *
     * @OA\Tag(name="VendorPlan")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a plan"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Plan not found"
     * )
     */
    public function create(
        string $vendorId,
        string $vendorPlanId
    ): Response {
        try {
            if ('current' == $vendorId) {
                /** @var Vendor $vendor */
                $vendor = $this->getUser();
            } else {
                // vendor fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));

            $this->vendorPlanService->delete($vendorPlan);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (VendorPlanNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
