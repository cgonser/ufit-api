<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Provider\ProgramAssetProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Service\ProgramAssetManager;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssetDeleteController extends AbstractController
{
    public function __construct(
        private ProgramAssetManager $programAssetManager,
        private ProgramAssetProvider $programAssetProvider,
        private VendorProgramProvider $vendorProgramProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Response(response=204, description="Deletes a program asset")
     * @OA\Response(response=404, description="Asset not found")
     */
    #[Route(
        path: '/vendors/{vendorId}/programs/{programId}/assets/{programAssetId}',
        name: 'vendor_program_assets_delete',
        methods: 'DELETE'
    )]
    public function delete(string $vendorId, string $programId, string $programAssetId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAsset = $this->programAssetProvider->getByProgramAndId($program, Uuid::fromString($programAssetId));

        $this->programAssetManager->delete($programAsset);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
