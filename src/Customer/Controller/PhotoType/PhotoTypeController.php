<?php

declare(strict_types=1);

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

#[Route(path: '/photo_types')]
class PhotoTypeController extends AbstractController
{
    public function __construct(
        private PhotoTypeProvider $photoTypeProvider,
        private PhotoTypeResponseMapper $photoTypeResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="PhotoType")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=PhotoTypeDto::class)))))
     * @Security(name="Bearer")
     */
    #[Route(name: 'photo_types_get', methods: 'GET')]
    public function getPhotoTypes(): ApiJsonResponse
    {
        $photoTypes = $this->photoTypeProvider->findAll();

        return new ApiJsonResponse(Response::HTTP_OK, $this->photoTypeResponseMapper->mapMultiple($photoTypes));
    }

    /**
     * @OA\Tag(name="PhotoType")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PhotoTypeDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/{photoTypeId}', name: 'photo_types_get_one', methods: 'GET')]
    public function getPhotoType(string $photoTypeId): Response
    {
        $photoType = $this->photoTypeProvider->get(Uuid::fromString($photoTypeId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->photoTypeResponseMapper->map($photoType));
    }
}
