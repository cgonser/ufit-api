<?php

namespace App\Customer\Controller\MeasureType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\MeasureTypeDto;
use App\Customer\Entity\MeasureType;
use App\Customer\Exception\MeasureTypeNotFoundException;
use App\Customer\Provider\MeasureTypeProvider;
use App\Customer\ResponseMapper\MeasureTypeResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeasureTypeController extends AbstractController
{
    private MeasureTypeResponseMapper $measureTypeResponseMapper;

    private MeasureTypeProvider $measureTypeProvider;

    public function __construct(
        MeasureTypeProvider $measureTypeProvider,
        MeasureTypeResponseMapper $measureTypeResponseMapper
    ) {
        $this->measureTypeResponseMapper = $measureTypeResponseMapper;
        $this->measureTypeProvider = $measureTypeProvider;
    }

    /**
     * @Route("/measure_types", methods="GET", name="measure_types_get")
     *
     * @OA\Tag(name="MeasureType")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information all measureTypes",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=MeasureTypeDto::class)))
     *     )*
     * )
     * @Security(name="Bearer")
     */
    public function getMeasureTypes(): Response
    {
        $measureTypes = $this->measureTypeProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->measureTypeResponseMapper->mapMultiple($measureTypes)
        );
    }

    /**
     * @Route("/measure_types/{measureTypeId}", methods="GET", name="measure_types_get_one")
     *
     * @OA\Tag(name="MeasureType")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a measure type",
     *     @OA\JsonContent(ref=@Model(type=MeasureTypeDto::class))
     * )
     * @Security(name="Bearer")
     */
    public function getMeasureType(string $measureTypeId): Response
    {
        try {
            if ('current' == $measureTypeId) {
                /** @var MeasureType $measureType */
                $measureType = $this->getUser();
            } else {
                // measureType fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            return new ApiJsonResponse(Response::HTTP_OK, $this->measureTypeResponseMapper->map($measureType));
        } catch (MeasureTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
