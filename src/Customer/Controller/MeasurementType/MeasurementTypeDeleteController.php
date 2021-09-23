<?php

declare(strict_types=1);

namespace App\Customer\Controller\MeasurementType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasurementTypeDto;
use App\Customer\Exception\MeasurementTypeNotFoundException;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\Service\MeasurementTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/measurement_types')]
class MeasurementTypeDeleteController extends AbstractController
{
    public function __construct(
        private MeasurementTypeService $measurementTypeService,
        private MeasurementTypeProvider $measurementTypeProvider
    ) {
    }

    /**
     * @OA\Tag(name="MeasurementType")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=MeasurementTypeDto::class)))
     * @OA\Response(response=404, description="Measurement type not found")
     */
    #[Route(path: '/{measurementTypeId}', name: 'measurement_types_delete', methods: 'DELETE')]
    public function delete(string $measurementTypeId): Response
    {
        $measurementType = $this->measurementTypeProvider->get(Uuid::fromString($measurementTypeId));

        $this->measurementTypeService->delete($measurementType);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
