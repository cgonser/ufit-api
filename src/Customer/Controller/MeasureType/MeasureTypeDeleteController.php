<?php

namespace App\Customer\Controller\MeasureType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasureTypeDto;
use App\Customer\Exception\MeasureTypeNotFoundException;
use App\Customer\Provider\MeasureTypeProvider;
use App\Customer\ResponseMapper\MeasureTypeResponseMapper;
use App\Customer\Service\MeasureTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeasureTypeDeleteController extends AbstractController
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
     * @Route("/measure_types/{measureTypeId}", methods="DELETE", name="measure_types_delete")
     *
     * @OA\Tag(name="MeasureType")
     * @OA\Response(
     *     response=200,
     *     description="Updates a measure type",
     *     @OA\JsonContent(ref=@Model(type=MeasureTypeDto::class))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Measure type not found"
     * )
     */
    public function delete(string $measureTypeId): Response
    {
        try {
            $measureType = $this->measureTypeProvider->get(Uuid::fromString($measureTypeId));

            $this->measureTypeService->delete($measureType);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (MeasureTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
