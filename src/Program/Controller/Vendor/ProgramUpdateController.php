<?php

declare(strict_types=1);

namespace App\Program\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Program\Dto\ProgramDto;
use App\Program\Provider\VendorProgramProvider;
use App\Program\Request\ProgramRequest;
use App\Program\ResponseMapper\ProgramResponseMapper;
use App\Program\Service\ProgramManager;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ProgramUpdateController extends AbstractController
{
    private VendorProgramProvider $programProvider;

    private ProgramManager $programManager;

    private ProgramResponseMapper $programResponseMapper;

    public function __construct(
        VendorProgramProvider $programProvider,
        ProgramManager $programManager,
        ProgramResponseMapper $programResponseMapper
    ) {
        $this->programResponseMapper = $programResponseMapper;
        $this->programProvider = $programProvider;
        $this->programManager = $programManager;
    }

    /**
     * @Route("/vendors/{vendorId}/programs/{programId}", methods="PUT", name="vendor_programs_update")
     *
     * @ParamConverter("programRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Program")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=ProgramRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a program",
     *     @OA\JsonContent(ref=@Model(type=ProgramDto::class))
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
        ProgramRequest $programRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByVendorAndId($vendor, Uuid::fromString($programId));

        $this->programManager->updateFromRequest($program, $programRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->map($program));
    }
}
