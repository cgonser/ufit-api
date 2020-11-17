<?php

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

class MeasurementTypeController extends AbstractController
{
    private MeasurementTypeResponseMapper $measurementTypeResponseMapper;

    private MeasurementTypeProvider $measurementTypeProvider;

    public function __construct(
        MeasurementTypeProvider $measurementTypeProvider,
        MeasurementTypeResponseMapper $measurementTypeResponseMapper
    ) {
        $this->measurementTypeResponseMapper = $measurementTypeResponseMapper;
        $this->measurementTypeProvider = $measurementTypeProvider;
    }

    /**
     * @Route("/measurement_types", methods="GET", name="measurement_types_get")
     *
     * @OA\Tag(name="MeasurementType")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information all measurementTypes",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=MeasurementTypeDto::class)))
     *     )*
     * )
     * @Security(name="Bearer")
     */
    public function getMeasurementTypes(): Response
    {
        $measurementTypes = $this->measurementTypeProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->measurementTypeResponseMapper->mapMultiple($measurementTypes)
        );
    }

    /**
     * @Route("/measurement_types/{measurementTypeId}", methods="GET", name="measurement_types_get_one")
     *
     * @OA\Tag(name="MeasurementType")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a measurement type",
     *     @OA\JsonContent(ref=@Model(type=MeasurementTypeDto::class))
     * )
     * @Security(name="Bearer")
     */
    public function getMeasurementType(string $measurementTypeId): Response
    {
        try {
            $measurementType = $this->measurementTypeProvider->get(
                Uuid::fromString($measurementTypeId)
            );

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->measurementTypeResponseMapper->map($measurementType)
            );
        } catch (MeasurementTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
