<?php

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Dto\ProgramAssetDto;
use App\Vendor\Entity\Vendor;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramAssetRequest;
use App\Program\ResponseMapper\ProgramAssetResponseMapper;
use App\Program\Service\ProgramAssetManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ProgramAssetCreateController extends AbstractController
{
    private VendorProgramProvider $programProvider;

    private ProgramAssetResponseMapper $programAssetResponseMapper;

    private ProgramAssetManager $programAssetManager;

    public function __construct(
        VendorProgramProvider $programProvider,
        ProgramAssetResponseMapper $programAssetResponseMapper,
        ProgramAssetManager $programAssetManager
    ) {
        $this->programProvider = $programProvider;
        $this->programAssetResponseMapper = $programAssetResponseMapper;
        $this->programAssetManager = $programAssetManager;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assets", methods="POST", name="program_assets_create")
     *
     * @ParamConverter("programAssetRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Program")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=ProgramAssetRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new program asset",
     *     @OA\JsonContent(ref=@Model(type=ProgramAssetDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Program not found"
     * )
     */
    public function create(
        string $vendorId,
        string $programId,
        ProgramAssetRequest $programAssetRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        $programAsset = $this->programAssetManager->createFromRequest($program, $programAssetRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->programAssetResponseMapper->map($programAsset)
        );
    }
}
