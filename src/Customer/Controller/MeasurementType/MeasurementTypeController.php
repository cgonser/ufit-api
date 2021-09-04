<?php

declare(strict_types=1);

namespace App\Customer\Controller\MeasurementType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasurementTypeDto;
use App\Customer\Entity\MeasurementType;
use App\Customer\Exception\MeasurementTypeNotFoundException;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\ResponseMapper\MeasurementTypeResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/measurement_types')]
class MeasurementTypeController extends AbstractController
{
    public function __construct(
        private MeasurementTypeProvider $measurementTypeProvider,
        private MeasurementTypeResponseMapper $measurementTypeResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="MeasurementType")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information all measurementTypes",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=MeasurementTypeDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(name: 'measurement_types_get', methods: 'GET')]
    public function getMeasurementTypes(): ApiJsonResponse
    {
        $measurementTypes = $this->measurementTypeProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->measurementTypeResponseMapper->mapMultiple($measurementTypes)
        );
    }

    /**
     * @OA\Tag(name="MeasurementType")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=MeasurementTypeDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/{measurementTypeId}', name: 'measurement_types_get_one', methods: 'GET')]
    public function getMeasurementType(string $measurementTypeId): Response
    {
        $measurementType = $this->measurementTypeProvider->get(Uuid::fromString($measurementTypeId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->measurementTypeResponseMapper->map($measurementType)
        );
    }
}
