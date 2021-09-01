<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Program\Dto\ProgramDto;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\VendorProgramSearchRequest;
use App\Program\ResponseMapper\ProgramResponseMapper;
use App\Vendor\Provider\VendorProvider;
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
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorProgramProvider $vendorProgramProvider,
        private ProgramResponseMapper $programResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=VendorProgramSearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=ProgramDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/vendors/{vendorId}/programs', name: 'vendor_programs_find', methods: 'GET')]
    #[ParamConverter(data: 'vendorProgramSearchRequest', converter: 'querystring')]
    public function getPrograms(
        string $vendorId,
        VendorProgramSearchRequest $vendorProgramSearchRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $vendorProgramSearchRequest->vendorId = $vendor->getId()->toString();
        $programs = $this->vendorProgramProvider->search($vendorProgramSearchRequest);
        $count = $this->vendorProgramProvider->count($vendorProgramSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programResponseMapper->mapMultiple($programs, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=ProgramDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/vendors/{vendorId}/programs/{programId}', name: 'vendor_programs_get_one', methods: 'GET')]
    public function getProgram(string $vendorId, string $programId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $program = $this->vendorProgramProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->map($program));
    }
}
