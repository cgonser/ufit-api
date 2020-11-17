<?php

namespace App\Customer\Controller\PhotoType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\PhotoTypeDto;
use App\Customer\Exception\PhotoTypeNotFoundException;
use App\Customer\Provider\PhotoTypeProvider;
use App\Customer\ResponseMapper\PhotoTypeResponseMapper;
use App\Customer\Service\PhotoTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhotoTypeDeleteController extends AbstractController
{
    private PhotoTypeService $photoTypeService;

    private PhotoTypeResponseMapper $photoTypeResponseMapper;

    private PhotoTypeProvider $photoTypeProvider;

    public function __construct(
        PhotoTypeService $photoTypeService,
        PhotoTypeProvider $photoTypeProvider,
        PhotoTypeResponseMapper $photoTypeResponseMapper
    ) {
        $this->photoTypeService = $photoTypeService;
        $this->photoTypeResponseMapper = $photoTypeResponseMapper;
        $this->photoTypeProvider = $photoTypeProvider;
    }

    /**
     * @Route("/photo_types/{photoTypeId}", methods="DELETE", name="photo_types_delete")
     *
     * @OA\Tag(name="PhotoType")
     * @OA\Response(
     *     response=200,
     *     description="Updates a photo type",
     *     @OA\JsonContent(ref=@Model(type=PhotoTypeDto::class))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Photo type not found"
     * )
     */
    public function delete(string $photoTypeId): Response
    {
        try {
            $photoType = $this->photoTypeProvider->get(Uuid::fromString($photoTypeId));

            $this->photoTypeService->delete($photoType);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (PhotoTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
