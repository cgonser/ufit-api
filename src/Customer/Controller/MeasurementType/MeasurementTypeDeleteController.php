<?php

declare(strict_types=1);

namespace App\Customer\Controller\MeasurementType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasurementTypeDto;
use App\Customer\Exception\MeasurementTypeNotFoundException;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\ResponseMapper\MeasurementTypeResponseMapper;
use App\Customer\Service\MeasurementTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeasurementTypeDeleteController extends AbstractController
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
     * @Route("/measurement_types/{measurementTypeId}", methods="DELETE", name="measurement_types_delete")
     *
     * @OA\Tag(name="MeasurementType")
     * @OA\Response(
     *     response=200,
     *     description="Updates a measurement type",
     *     @OA\JsonContent(ref=@Model(type=MeasurementTypeDto::class))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Measurement type not found"
     * )
     */
    public function delete(string $measurementTypeId): Response
    {
        try {
            $measurementType = $this->measurementTypeProvider->get(Uuid::fromString($measurementTypeId));

            $this->measurementTypeService->delete($measurementType);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (MeasurementTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
