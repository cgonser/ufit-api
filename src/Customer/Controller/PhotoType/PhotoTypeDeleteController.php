<?php

declare(strict_types=1);

namespace App\Customer\Controller\PhotoType;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\PhotoTypeDto;
use App\Customer\Exception\PhotoTypeNotFoundException;
use App\Customer\Provider\PhotoTypeProvider;
use App\Customer\Service\PhotoTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/photo_types')]
class PhotoTypeDeleteController extends AbstractController
{
    public function __construct(
        private PhotoTypeService $photoTypeService,
        private PhotoTypeProvider $photoTypeProvider
    ) {
    }

    /**
     * @OA\Tag(name="PhotoType")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PhotoTypeDto::class)))
     * @OA\Response(response=404, description="Photo type not found")
     */
    #[Route(path: '/{photoTypeId}', name: 'photo_types_delete', methods: 'DELETE')]
    public function delete(string $photoTypeId): Response
    {
        $photoType = $this->photoTypeProvider->get(Uuid::fromString($photoTypeId));

        $this->photoTypeService->delete($photoType);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
