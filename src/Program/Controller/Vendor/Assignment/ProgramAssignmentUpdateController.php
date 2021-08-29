<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Assignment;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Provider\ProgramAssignmentProvider;
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

class ProgramAssignmentUpdateController extends AbstractController
{
    private ProgramAssignmentManager $programAssignmentManager;

    private ProgramAssignmentProvider $programAssignmentProvider;

    private ProgramAssignmentResponseMapper $programAssignmentResponseMapper;

    private VendorProgramProvider $programProvider;

    public function __construct(
        ProgramAssignmentManager $programAssignmentManager,
        ProgramAssignmentProvider $programAssignmentProvider,
        ProgramAssignmentResponseMapper $programAssignmentResponseMapper,
        VendorProgramProvider $programProvider
    ) {
        $this->programAssignmentManager = $programAssignmentManager;
        $this->programAssignmentProvider = $programAssignmentProvider;
        $this->programProvider = $programProvider;
        $this->programAssignmentResponseMapper = $programAssignmentResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assignments/{programAssignmentId}", methods="PUT", name="vendor_program_assignments_update")
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
     *     response=200,
     *     description="Updates a program assignment",
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
    public function update(
        string $vendorId,
        string $programId,
        string $programAssignmentId,
        ProgramAssignmentRequest $programAssignmentRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        $programAssignment = $this->programAssignmentProvider->getByProgramAndId(
            $program,
            Uuid::fromString($programAssignmentId)
        );

        $this->programAssignmentManager->updateFromRequest($programAssignment, $programAssignmentRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programAssignmentResponseMapper->map($programAssignment)
        );
    }
}
