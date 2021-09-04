<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Asset;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Dto\ProgramAssetDto;
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
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ProgramAssetCreateController extends AbstractController
{
    public function __construct(
        private VendorProgramProvider $vendorProgramProvider,
        private ProgramAssetResponseMapper $programAssetResponseMapper,
        private ProgramAssetRequestManager $programAssetRequestManager,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=ProgramAssetRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=ProgramAssetDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Program not found")
     */
    #[Route(path: '/vendors/{vendorId}/programs/{programId}/assets', name: 'program_assets_create', methods: 'POST')]
    #[ParamConverter(
        data: 'programAssetRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        string $vendorId,
        string $programId,
        ProgramAssetRequest $programAssetRequest,
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAsset = $this->programAssetRequestManager->createFromRequest($program, $programAssetRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->programAssetResponseMapper->map($programAsset));
    }
}
