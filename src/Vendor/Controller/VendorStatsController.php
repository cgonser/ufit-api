<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorStatsDto;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\ResponseMapper\VendorStatsResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorStatsController extends AbstractController
{
    private VendorStatsResponseMapper $vendorStatsResponseMapper;

    public function __construct(
        VendorStatsResponseMapper $vendorStatsResponseMapper
    ) {
        $this->vendorStatsResponseMapper = $vendorStatsResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}/stats", methods="GET", name="vendor_stats_get")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Vendor / Stats")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(ref=@Model(type=VendorStatsDto::class))
     * )
     */
    public function getVendorStats(string $vendorId): Response
    {
        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorStatsResponseMapper->map()
        );
    }
}
