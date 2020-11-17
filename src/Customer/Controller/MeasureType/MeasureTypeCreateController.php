<?php

namespace App\Customer\Controller\MeasureType;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasureTypeDto;
use App\Customer\Exception\MeasureTypeAlreadyExistsException;
use App\Customer\Request\MeasureTypeRequest;
use App\Customer\ResponseMapper\MeasureTypeResponseMapper;
use App\Customer\Service\MeasureTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MeasureTypeCreateController extends AbstractController
{
    private MeasureTypeService $measureTypeService;

    private MeasureTypeResponseMapper $measureTypeResponseMapper;

    public function __construct(
        MeasureTypeService $measureTypeService,
        MeasureTypeResponseMapper $measureTypeResponseMapper
    ) {
        $this->measureTypeService = $measureTypeService;
        $this->measureTypeResponseMapper = $measureTypeResponseMapper;
    }

    /**
     * @Route("/measure_types", methods="POST", name="measure_types_create")
     *
     * @ParamConverter("measureTypeRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="MeasureType")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=MeasureTypeRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new measure type",
     *     @OA\JsonContent(ref=@Model(type=MeasureTypeDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        MeasureTypeRequest $measureTypeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $measureType = $this->measureTypeService->create($measureTypeRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->measureTypeResponseMapper->map($measureType)
            );
        } catch (MeasureTypeAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
