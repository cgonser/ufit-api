<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Dto\ProgramAssetDto;
use App\Program\Provider\ProgramAssetProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramAssetRequest;
use App\Program\ResponseMapper\ProgramAssetResponseMapper;
use App\Program\Service\ProgramAssetRequestManager;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssetUpdateController extends AbstractController
{
    public function __construct(
        private ProgramAssetRequestManager $programAssetRequestManager,
        private ProgramAssetProvider $programAssetProvider,
        private VendorProgramProvider $vendorProgramProvider,
        private ProgramAssetResponseMapper $programAssetResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=ProgramAssetRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=ProgramAssetDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(
        path: '/vendors/{vendorId}/programs/{programId}/assets/{programAssetId}',
        name: 'vendor_program_assets_update',
        methods: 'PATCH'
    )]
    #[ParamConverter(
        data: 'programAssetRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        string $vendorId,
        string $programId,
        string $programAssetId,
        ProgramAssetRequest $programAssetRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAsset = $this->programAssetProvider->getByProgramAndId($program, Uuid::fromString($programAssetId));

        $this->programAssetRequestManager->updateFromRequest($programAsset, $programAssetRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->programAssetResponseMapper->map($programAsset));
    }
}
