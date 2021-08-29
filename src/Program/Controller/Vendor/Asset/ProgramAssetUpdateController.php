<?php

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Dto\ProgramAssetDto;
use App\Program\Provider\ProgramAssetProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramAssetRequest;
use App\Program\ResponseMapper\ProgramAssetResponseMapper;
use App\Program\Service\ProgramAssetRequestManager;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssetUpdateController extends AbstractController
{
    private ProgramAssetRequestManager $programAssetRequestManager;
    private ProgramAssetProvider $programAssetProvider;
    private VendorProgramProvider $programProvider;
    private ProgramAssetResponseMapper $programAssetResponseMapper;

    public function __construct(
        ProgramAssetRequestManager $programAssetRequestManager,
        ProgramAssetProvider $programAssetProvider,
        VendorProgramProvider $programProvider,
        ProgramAssetResponseMapper $programAssetResponseMapper
    ) {
        $this->programAssetRequestManager = $programAssetRequestManager;
        $this->programAssetProvider = $programAssetProvider;
        $this->programProvider = $programProvider;
        $this->programAssetResponseMapper = $programAssetResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assets/{programAssetId}", methods="PATCH", name="vendor_program_assets_update")
     * @ParamConverter("programAssetRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"={"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Program")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=ProgramAssetRequest::class)))
     * @OA\Response(response=200, description="Updates an asset", @OA\JsonContent(ref=@Model(type=ProgramAssetDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function update(
        string $vendorId,
        string $programId,
        string $programAssetId,
        ProgramAssetRequest $programAssetRequest
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

        $this->programAssetRequestManager->updateFromRequest($programAsset, $programAssetRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programAssetResponseMapper->map($programAsset)
        );
    }
}
