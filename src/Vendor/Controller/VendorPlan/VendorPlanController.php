<?php

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorPlanController extends AbstractController
{
    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    private VendorPlanProvider $vendorPlanProvider;

    public function __construct(
        VendorPlanProvider $vendorPlanProvider,
        VendorPlanResponseMapper $vendorPlanResponseMapper
    ) {
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
        $this->vendorPlanProvider = $vendorPlanProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/plans", methods="GET", name="vendors_plans_get")
     *
     * @OA\Tag(name="VendorPlan")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a vendor plans",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=VendorPlanDto::class)))
     *     )
     * )
     *
     * @Security(name="Bearer")
     */
    public function getVendorPlans(string $vendorId): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorPlans = $this->vendorPlanProvider->findVendorPlans($vendor);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorPlanResponseMapper->mapMultiple($vendorPlans));
    }
}
