<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Provider\ProgramAssetProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\ResponseMapper\ProgramAssetResponseMapper;
use App\Program\Service\ProgramAssetManager;
use App\Vendor\Provider\VendorProvider;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssetUploadController extends AbstractController
{
    public function __construct(
        private ProgramAssetManager $programAssetManager,
        private ProgramAssetProvider $programAssetProvider,
        private VendorProgramProvider $vendorProgramProvider,
        private ProgramAssetResponseMapper $programAssetResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=204, description="Uploads an asset")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(
        path: '/vendors/{vendorId}/programs/{programId}/assets/{programAssetId}/file',
        name: 'vendor_program_assets_upload',
        methods: 'PUT'
    )]
    public function upload(
        string $vendorId,
        string $programId,
        string $programAssetId,
        Request $request
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAsset = $this->programAssetProvider->getByProgramAndId($program, Uuid::fromString($programAssetId));

        $this->programAssetManager->uploadAsset(
            $programAsset,
            $request->getContent(),
            $request->headers->get('Content-Type')
        );

        return new ApiJsonResponse(Response::HTTP_OK, $this->programAssetResponseMapper->map($programAsset));
    }
}
