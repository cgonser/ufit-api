<?php

namespace App\Program\Controller\Vendor\Assignment;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Program\Dto\ProgramDto;
use App\Program\Provider\ProgramAssignmentProvider;
use App\Program\Provider\VendorProgramProvider;
use App\Program\ResponseMapper\ProgramAssignmentResponseMapper;
use App\Vendor\Entity\Vendor;
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
    private ProgramAssignmentProvider $programAssignmentProvider;

    private ProgramAssignmentResponseMapper $programAssignmentResponseMapper;

    private VendorProgramProvider $programProvider;

    public function __construct(
        ProgramAssignmentProvider $programAssignmentProvider,
        ProgramAssignmentResponseMapper $programAssignmentResponseMapper,
        VendorProgramProvider $programProvider
    ) {
        $this->programAssignmentProvider = $programAssignmentProvider;
        $this->programAssignmentResponseMapper = $programAssignmentResponseMapper;
        $this->programProvider = $programProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}/assignments", methods="GET", name="vendor_program_assignments_find")
     *
     * @ParamConverter("searchRequest", converter="querystring")
     *
     * @OA\Tag(name="Program")
     *
     * @OA\Parameter(
     *     in="query",
     *     name="filters",
     *     @OA\Schema(ref=@Model(type=SearchRequest::class))
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all assignments of a given program",
     *     @OA\Header(
     *         header="X-Total-Count",
     *         @OA\Schema(
     *             type="int"
     *         )
     *     ),
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=ProgramDto::class)))
     *     )
     * )
     *
     * @Security(name="Bearer")
     */
    public function getPrograms(string $vendorId, string $programId, SearchRequest $searchRequest): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        $programAssignments = $this->programAssignmentProvider->searchProgramAssignments($program, $searchRequest);
        $count = $this->programAssignmentProvider->countProgramAssignments($program, $searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programAssignmentResponseMapper->mapMultiple($programAssignments, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
