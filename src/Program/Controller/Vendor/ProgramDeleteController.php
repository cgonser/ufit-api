<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Service\ProgramManager;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramDeleteController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private ProgramManager $programManager,
        private VendorProgramProvider $vendorProgramProvider
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Response(response=204, description="Deletes a program")
     * @OA\Response(response=404, description="Program not found")
     */
    #[Route(path: '/vendors/{vendorId}/programs/{programId}', name: 'vendor_programs_delete', methods: 'DELETE')]
    public function create(string $vendorId, string $programId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $this->programManager->delete($program);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
