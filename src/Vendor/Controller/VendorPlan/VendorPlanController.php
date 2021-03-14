<?php

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorPlanController extends AbstractController
{
    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    private VendorPlanProvider $vendorPlanProvider;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorPlanProvider $vendorPlanProvider,
        VendorPlanResponseMapper $vendorPlanResponseMapper,
        VendorProvider $vendorProvider
    ) {
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/plans", methods="GET", name="vendors_plans_get")
     *
     * @OA\Tag(name="Vendor / Plan")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a vendor plans",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=VendorPlanDto::class)))
     *     )
     * )
     */
    public function getVendorPlans(string $vendorId): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        }

        $vendorPlans = $this->vendorPlanProvider->findVendorPlans($vendor);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorPlanResponseMapper->mapMultiple($vendorPlans));
    }

    /**
     * @Route("/vendors/{vendorId}/plans/{vendorPlanId}", methods="GET", name="vendors_plans_get_one")
     *
     * @OA\Tag(name="VendorPlan")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a plan",
     *     @OA\JsonContent(ref=@Model(type=VendorPlanDto::class))
     * )
     */
    public function getVendorPlan(string $vendorId, string $vendorPlanId): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        }

        $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorPlanResponseMapper->map($vendorPlan));
    }
}
