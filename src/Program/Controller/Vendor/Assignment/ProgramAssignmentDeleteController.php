<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Assignment;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Provider\ProgramAssignmentProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Service\ProgramAssignmentManager;
use App\Vendor\Entity\Vendor;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssignmentDeleteController extends AbstractController
{
    private ProgramAssignmentManager $programAssignmentManager;

    private ProgramAssignmentProvider $programAssignmentProvider;

    private VendorProgramProvider $programProvider;

    public function __construct(
        ProgramAssignmentManager $programAssignmentManager,
        ProgramAssignmentProvider $programAssignmentProvider,
        VendorProgramProvider $programProvider
    ) {
        $this->programAssignmentManager = $programAssignmentManager;
        $this->programAssignmentProvider = $programAssignmentProvider;
        $this->programProvider = $programProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assignments/{programAssignmentId}", methods="DELETE", name="vendor_program_assignments_delete")
     *
     * @OA\Tag(name="Program")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a program assignment"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Assignment not found"
     * )
     */
    public function delete(string $vendorId, string $programId, string $programAssignmentId): Response
    {
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

        $this->programAssignmentManager->delete($programAssignment);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
