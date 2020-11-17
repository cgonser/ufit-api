<?php

namespace App\Customer\Controller\MeasureType;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasureTypeDto;
use App\Customer\Exception\MeasureTypeAlreadyExistsException;
use App\Customer\Exception\MeasureTypeNotFoundException;
use App\Customer\Provider\MeasureTypeProvider;
use App\Customer\Request\MeasureTypeRequest;
use App\Customer\ResponseMapper\MeasureTypeResponseMapper;
use App\Customer\Service\MeasureTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MeasureTypeUpdateController extends AbstractController
{
    private MeasureTypeService $measureTypeService;

    private MeasureTypeResponseMapper $measureTypeResponseMapper;

    private MeasureTypeProvider $measureTypeProvider;

    public function __construct(
        MeasureTypeService $measureTypeService,
        MeasureTypeProvider $measureTypeProvider,
        MeasureTypeResponseMapper $measureTypeResponseMapper
    ) {
        $this->measureTypeService = $measureTypeService;
        $this->measureTypeResponseMapper = $measureTypeResponseMapper;
        $this->measureTypeProvider = $measureTypeProvider;
    }

    /**
     * @Route("/measure_types/{measureTypeId}", methods="PUT", name="measure_types_update")
     *
     * @ParamConverter("measureTypeRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="MeasureType")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=MeasureTypeRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a measure type",
     *     @OA\JsonContent(ref=@Model(type=MeasureTypeDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function update(
        string $measureTypeId,
        MeasureTypeRequest $measureTypeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $measureType = $this->measureTypeProvider->get(Uuid::fromString($measureTypeId));

            $this->measureTypeService->update($measureType, $measureTypeRequest);

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->measureTypeResponseMapper->map($measureType)
            );
        } catch (MeasureTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (MeasureTypeAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
