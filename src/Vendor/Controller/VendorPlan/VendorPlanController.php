<?php

declare(strict_types=1);

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/plans')]
class VendorPlanController extends AbstractController
{
    public function __construct(
        private VendorPlanProvider $vendorPlanProvider,
        private VendorPlanResponseMapper $vendorPlanResponseMapper,
        private VendorProvider $vendorProvider
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Plan")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about all vendor's plans",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=VendorPlanDto::class)))
     *     )
     * )
     */
    #[Route(name: 'vendors_plans_get', methods: 'GET')]
    public function getVendorPlans(string $vendorId): ApiJsonResponse
    {
        $vendor = 'current' === $vendorId
            ? $this->getUser()
            : $this->vendorProvider->get(Uuid::fromString($vendorId));

        $vendorPlans = $this->vendorPlanProvider->findVendorPlans($vendor);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorPlanResponseMapper->mapMultiple($vendorPlans));
    }

    /**
     * @OA\Tag(name="VendorPlan")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a plan",
     *     @OA\JsonContent(ref=@Model(type=VendorPlanDto::class))
     * )
     */
    #[Route(path: '/{vendorPlanId}', name: 'vendors_plans_get_one', methods: 'GET')]
    public function getVendorPlan(string $vendorId, string $vendorPlanId): ApiJsonResponse
    {
        $vendor = 'current' === $vendorId
            ? $this->getUser()
            : $this->vendorProvider->get(Uuid::fromString($vendorId));

        $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorPlanResponseMapper->map($vendorPlan));
    }
}
