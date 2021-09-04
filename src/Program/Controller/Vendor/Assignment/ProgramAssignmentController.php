<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor\Assignment;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Provider\ProgramAssignmentProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramAssignmentSearchRequest;
use App\Program\ResponseMapper\ProgramAssignmentResponseMapper;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramAssignmentController extends AbstractController
{
    public function __construct(
        private ProgramAssignmentProvider $programAssignmentProvider,
        private ProgramAssignmentResponseMapper $programAssignmentResponseMapper,
        private VendorProgramProvider $vendorProgramProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=ProgramAssignmentSearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Returns all assignments of a given program",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=ProgramAssignmentDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/vendors/{vendorId}/programs/{programId}/assignments',
        name: 'vendor_program_assignments_find',
        methods: 'GET'
    )]
    #[ParamConverter(data: 'programAssignmentSearchRequest', converter: 'querystring')]
    public function getPrograms(
        string $vendorId,
        string $programId,
        ProgramAssignmentSearchRequest $programAssignmentSearchRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));
        $programAssignmentSearchRequest->programId = $programId;

        $programAssignments = $this->programAssignmentProvider->searchProgramAssignments(
            $program,
            $programAssignmentSearchRequest
        );
        $count = $this->programAssignmentProvider->countProgramAssignments($program, $programAssignmentSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programAssignmentResponseMapper->mapMultiple($programAssignments, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
