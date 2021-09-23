<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Assignment;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Provider\ProgramAssignmentProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Service\ProgramAssignmentManager;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssignmentDeleteController extends AbstractController
{
    public function __construct(
        private ProgramAssignmentManager $programAssignmentManager,
        private ProgramAssignmentProvider $programAssignmentProvider,
        private VendorProgramProvider $vendorProgramProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Response(response=204, description="Deletes a program assignment")
     * @OA\Response(response=404, description="Assignment not found")
     */
    #[Route(
        path: '/vendors/{vendorId}/programs/{programId}/assignments/{programAssignmentId}',
        name: 'vendor_program_assignments_delete',
        methods: 'DELETE'
    )]
    public function delete(string $vendorId, string $programId, string $programAssignmentId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAssignment = $this->programAssignmentProvider->getByProgramAndId(
            $program,
            Uuid::fromString($programAssignmentId)
        );
        $this->programAssignmentManager->delete($programAssignment);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
