<?php

namespace App\Customer\Controller\MeasurementType;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasurementTypeDto;
use App\Customer\Exception\MeasurementTypeAlreadyExistsException;
use App\Customer\Exception\MeasurementTypeNotFoundException;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\Request\MeasurementTypeRequest;
use App\Customer\ResponseMapper\MeasurementTypeResponseMapper;
use App\Customer\Service\MeasurementTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MeasurementTypeUpdateController extends AbstractController
{
    private MeasurementTypeService $measurementTypeService;

    private MeasurementTypeResponseMapper $measurementTypeResponseMapper;

    private MeasurementTypeProvider $measurementTypeProvider;

    public function __construct(
        MeasurementTypeService $measurementTypeService,
        MeasurementTypeProvider $measurementTypeProvider,
        MeasurementTypeResponseMapper $measurementTypeResponseMapper
    ) {
        $this->measurementTypeService = $measurementTypeService;
        $this->measurementTypeResponseMapper = $measurementTypeResponseMapper;
        $this->measurementTypeProvider = $measurementTypeProvider;
    }

    /**
     * @Route("/measurement_types/{measurementTypeId}", methods="PUT", name="measurement_types_update")
     *
     * @ParamConverter("measurementTypeRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="MeasurementType")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=MeasurementTypeRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a measurement type",
     *     @OA\JsonContent(ref=@Model(type=MeasurementTypeDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function update(
        string $measurementTypeId,
        MeasurementTypeRequest $measurementTypeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $measurementType = $this->measurementTypeProvider->get(Uuid::fromString($measurementTypeId));

            $this->measurementTypeService->update($measurementType, $measurementTypeRequest);

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->measurementTypeResponseMapper->map($measurementType)
            );
        } catch (MeasurementTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (MeasurementTypeAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
