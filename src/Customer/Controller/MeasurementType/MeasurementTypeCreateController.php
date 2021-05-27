<?php

namespace App\Customer\Controller\MeasurementType;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasurementTypeDto;
use App\Customer\Exception\MeasurementTypeAlreadyExistsException;
use App\Customer\Request\MeasurementTypeRequest;
use App\Customer\ResponseMapper\MeasurementTypeResponseMapper;
use App\Customer\Service\MeasurementTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MeasurementTypeCreateController extends AbstractController
{
    private MeasurementTypeService $measurementTypeService;

    private MeasurementTypeResponseMapper $measurementTypeResponseMapper;

    public function __construct(
        MeasurementTypeService $measurementTypeService,
        MeasurementTypeResponseMapper $measurementTypeResponseMapper
    ) {
        $this->measurementTypeService = $measurementTypeService;
        $this->measurementTypeResponseMapper = $measurementTypeResponseMapper;
    }

    /**
     * @Route("/measurement_types", methods="POST", name="measurement_types_create")
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
     *     response=201,
     *     description="Creates a new measurement type",
     *     @OA\JsonContent(ref=@Model(type=MeasurementTypeDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        MeasurementTypeRequest $measurementTypeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $measurementType = $this->measurementTypeService->create($measurementTypeRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->measurementTypeResponseMapper->map($measurementType)
            );
        } catch (MeasurementTypeAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
