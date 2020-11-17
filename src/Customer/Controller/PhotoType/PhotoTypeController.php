<?php

namespace App\Customer\Controller\PhotoType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\PhotoTypeDto;
use App\Customer\Entity\PhotoType;
use App\Customer\Exception\PhotoTypeNotFoundException;
use App\Customer\Provider\PhotoTypeProvider;
use App\Customer\ResponseMapper\PhotoTypeResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhotoTypeController extends AbstractController
{
    private PhotoTypeResponseMapper $photoTypeResponseMapper;

    private PhotoTypeProvider $photoTypeProvider;

    public function __construct(
        PhotoTypeProvider $photoTypeProvider,
        PhotoTypeResponseMapper $photoTypeResponseMapper
    ) {
        $this->photoTypeResponseMapper = $photoTypeResponseMapper;
        $this->photoTypeProvider = $photoTypeProvider;
    }

    /**
     * @Route("/photo_types", methods="GET", name="photo_types_get")
     *
     * @OA\Tag(name="PhotoType")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information all photoTypes",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=PhotoTypeDto::class)))
     *     )*
     * )
     * @Security(name="Bearer")
     */
    public function getPhotoTypes(): Response
    {
        $photoTypes = $this->photoTypeProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->photoTypeResponseMapper->mapMultiple($photoTypes)
        );
    }

    /**
     * @Route("/photo_types/{photoTypeId}", methods="GET", name="photo_types_get_one")
     *
     * @OA\Tag(name="PhotoType")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a photo type",
     *     @OA\JsonContent(ref=@Model(type=PhotoTypeDto::class))
     * )
     * @Security(name="Bearer")
     */
    public function getPhotoType(string $photoTypeId): Response
    {
        try {
            $photoType = $this->photoTypeProvider->get(
                Uuid::fromString($photoTypeId)
            );

            return new ApiJsonResponse(Response::HTTP_OK, $this->photoTypeResponseMapper->map($photoType));
        } catch (PhotoTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
