<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Dto\ProgramDto;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramRequest;
use App\Program\ResponseMapper\ProgramResponseMapper;
use App\Program\Service\ProgramManager;
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

class ProgramUpdateController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorProgramProvider $vendorProgramProvider,
        private ProgramManager $programManager,
        private ProgramResponseMapper $programResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=ProgramRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=ProgramDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Program not found")
     */
    #[Route(path: '/vendors/{vendorId}/programs/{programId}', name: 'vendor_programs_update', methods: 'PUT')]
    #[ParamConverter(
        data: 'programRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        string $vendorId,
        string $programId,
        ProgramRequest $programRequest,
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $this->programManager->updateFromRequest($program, $programRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->map($program));
    }
}
