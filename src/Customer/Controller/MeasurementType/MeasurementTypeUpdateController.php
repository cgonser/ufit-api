<?php

declare(strict_types=1);

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

#[Route(path: '/measurement_types')]
class MeasurementTypeUpdateController extends AbstractController
{
    public function __construct(
        private MeasurementTypeService $measurementTypeService,
        private MeasurementTypeProvider $measurementTypeProvider,
        private MeasurementTypeResponseMapper $measurementTypeResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="MeasurementType")
     * @OA\RequestBody( required=true, @OA\JsonContent(ref=@Model(type=MeasurementTypeRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=MeasurementTypeDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/{measurementTypeId}', name: 'measurement_types_update', methods: 'PUT')]
    #[ParamConverter(data: 'measurementTypeRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $measurementTypeId,
        MeasurementTypeRequest $measurementTypeRequest,
    ): Response {
        $measurementType = $this->measurementTypeProvider->get(Uuid::fromString($measurementTypeId));

        $this->measurementTypeService->update($measurementType, $measurementTypeRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->measurementTypeResponseMapper->map($measurementType)
        );
    }
}
