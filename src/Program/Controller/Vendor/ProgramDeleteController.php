<?php

namespace App\Program\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Provider\ProgramProvider;
use App\Program\Service\ProgramManager;
use App\Vendor\Entity\Vendor;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramDeleteController extends AbstractController
{
    private ProgramManager $programManager;

    private ProgramProvider $programProvider;

    public function __construct(
        ProgramManager $programManager,
        ProgramProvider $programProvider
    ) {
        $this->programManager = $programManager;
        $this->programProvider = $programProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}", methods="DELETE", name="vendor_programs_delete")
     *
     * @OA\Tag(name="Program")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a program"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Program not found"
     * )
     */
    public function create(string $vendorId, string $programId): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        $this->programManager->delete($program);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
