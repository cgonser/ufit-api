<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Provider\ProgramAssetProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\ResponseMapper\ProgramAssetResponseMapper;
use App\Program\Service\ProgramAssetManager;
use App\Vendor\Entity\Vendor;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssetUploadController extends AbstractController
{
    private ProgramAssetManager $programAssetManager;
    private ProgramAssetProvider $programAssetProvider;
    private VendorProgramProvider $programProvider;
    private ProgramAssetResponseMapper $programAssetResponseMapper;

    public function __construct(
        ProgramAssetManager $programAssetManager,
        ProgramAssetProvider $programAssetProvider,
        VendorProgramProvider $programProvider,
        ProgramAssetResponseMapper $programAssetResponseMapper
    ) {
        $this->programAssetManager = $programAssetManager;
        $this->programAssetProvider = $programAssetProvider;
        $this->programProvider = $programProvider;
        $this->programAssetResponseMapper = $programAssetResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assets/{programAssetId}/file", methods="PUT", name="vendor_program_assets_upload")
     *
     * @OA\Tag(name="Program")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=204, description="Uploads an asset")
     * @OA\Response(response=400, description="Invalid input")
     */
    public function upload(
        string $vendorId,
        string $programId,
        string $programAssetId,
        Request $request
    ): Response {
        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAsset = $this->programAssetProvider->getByProgramAndId($program, Uuid::fromString($programAssetId));

        $this->programAssetManager->uploadAsset(
            $programAsset,
            $request->getContent(),
            $request->headers->get('Content-Type')
        );

        return new ApiJsonResponse(Response::HTTP_OK, $this->programAssetResponseMapper->map($programAsset));
    }
}
