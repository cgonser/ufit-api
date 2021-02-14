<?php

namespace App\Program\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Program\Dto\ProgramDto;
use App\Program\ResponseMapper\ProgramResponseMapper;
use App\Vendor\Entity\Vendor;
use App\Program\Provider\ProgramProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramController extends AbstractController
{
    private ProgramProvider $programProvider;

    private ProgramResponseMapper $programResponseMapper;

    private CustomerResponseMapper $customerResponseMapper;

    public function __construct(
        ProgramProvider $programProvider,
        ProgramResponseMapper $programResponseMapper,
        CustomerResponseMapper $customerResponseMapper
    ) {
        $this->programProvider = $programProvider;
        $this->programResponseMapper = $programResponseMapper;
        $this->customerResponseMapper = $customerResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}/programs", methods="GET", name="vendor_programs_find")
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
     *     description="Returns all programs of a given vendor",
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
    public function getPrograms(string $vendorId, SearchRequest $searchRequest): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $programs = $this->programProvider->searchVendorPrograms($vendor, $searchRequest);
        $count = $this->programProvider->countVendorPrograms($vendor, $searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programResponseMapper->mapMultiple($programs, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}", methods="GET", name="vendor_programs_get_one")
     *
     * @OA\Tag(name="Program")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a program",
     *     @OA\JsonContent(ref=@Model(type=ProgramDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getProgram(string $vendorId, string $programId): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programResponseMapper->map($program)
        );
    }
}
