<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Assignment;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramAssignmentRequest;
use App\Program\ResponseMapper\ProgramAssignmentResponseMapper;
use App\Program\Service\ProgramAssignmentManager;
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

class ProgramAssignmentCreateController extends AbstractController
{
    public function __construct(
        private VendorProgramProvider $vendorProgramProvider,
        private ProgramAssignmentResponseMapper $programAssignmentResponseMapper,
        private ProgramAssignmentManager $programAssignmentManager,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Program / Assignment")
     * @OA\RequestBody( required=true, @OA\JsonContent(ref=@Model(type=ProgramAssignmentRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=ProgramAssignmentDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Program not found")
     */
    #[Route(
        path: '/vendors/{vendorId}/programs/{programId}/assignments',
        name: 'program_assignments_create',
        methods: 'POST'
    )]
    #[ParamConverter(
        data: 'programAssignmentRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        string $vendorId,
        string $programId,
        ProgramAssignmentRequest $programAssignmentRequest,
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAssignment = $this->programAssignmentManager->createFromRequest($program, $programAssignmentRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->programAssignmentResponseMapper->map($programAssignment)
        );
    }
}
