<?php

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Provider\ProgramAssetProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Service\ProgramAssetManager;
use App\Vendor\Entity\Vendor;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssetDeleteController extends AbstractController
{
    private ProgramAssetManager $programAssetManager;

    private ProgramAssetProvider $programAssetProvider;

    private VendorProgramProvider $programProvider;

    public function __construct(
        ProgramAssetManager $programAssetManager,
        ProgramAssetProvider $programAssetProvider,
        VendorProgramProvider $programProvider
    ) {
        $this->programAssetManager = $programAssetManager;
        $this->programAssetProvider = $programAssetProvider;
        $this->programProvider = $programProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assets/{programAssetId}", methods="DELETE", name="vendor_program_assets_delete")
     *
     * @OA\Tag(name="Program")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a program asset"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Asset not found"
     * )
     */
    public function delete(string $vendorId, string $programId, string $programAssetId): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        $programAsset = $this->programAssetProvider->getByProgramAndId($program, Uuid::fromString($programAssetId));

        $this->programAssetManager->delete($programAsset);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
