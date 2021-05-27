<?php

namespace App\Program\Controller\Vendor\Assignment;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramAssignmentRequest;
use App\Program\ResponseMapper\ProgramAssignmentResponseMapper;
use App\Program\Service\ProgramAssignmentManager;
use App\Vendor\Entity\Vendor;
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
    private VendorProgramProvider $programProvider;

    private ProgramAssignmentResponseMapper $programAssignmentResponseMapper;

    private ProgramAssignmentManager $programAssignmentManager;

    public function __construct(
        VendorProgramProvider $programProvider,
        ProgramAssignmentResponseMapper $programAssignmentResponseMapper,
        ProgramAssignmentManager $programAssignmentManager
    ) {
        $this->programProvider = $programProvider;
        $this->programAssignmentResponseMapper = $programAssignmentResponseMapper;
        $this->programAssignmentManager = $programAssignmentManager;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assignments", methods="POST", name="program_assignments_create")
     *
     * @ParamConverter("programAssignmentRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Program / Assignment")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=ProgramAssignmentRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new program assignment",
     *     @OA\JsonContent(ref=@Model(type=ProgramAssignmentDto::class))
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
        ProgramAssignmentRequest $programAssignmentRequest,
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

        $programAssignment = $this->programAssignmentManager->createFromRequest($program, $programAssignmentRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->programAssignmentResponseMapper->map($programAssignment)
        );
    }
}
