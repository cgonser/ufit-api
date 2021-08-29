<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorStatsDto;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\ResponseMapper\VendorStatsResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorStatsController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorStatsResponseMapper $vendorStatsResponseMapper
    ) {
    }

    /**
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Vendor / Stats")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=VendorStatsDto::class)))
     */
    #[Route(path: '/vendors/{vendorId}/stats', methods: 'GET', name: 'vendor_stats_get')]
    public function getVendorStats(string $vendorId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorStatsResponseMapper->map());
    }
}
