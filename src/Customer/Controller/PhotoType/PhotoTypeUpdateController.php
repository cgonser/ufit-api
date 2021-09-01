<?php

declare(strict_types=1);

namespace App\Customer\Controller\PhotoType;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\PhotoTypeDto;
use App\Customer\Exception\PhotoTypeAlreadyExistsException;
use App\Customer\Exception\PhotoTypeNotFoundException;
use App\Customer\Provider\PhotoTypeProvider;
use App\Customer\Request\PhotoTypeRequest;
use App\Customer\ResponseMapper\PhotoTypeResponseMapper;
use App\Customer\Service\PhotoTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/photo_types')]
class PhotoTypeUpdateController extends AbstractController
{
    public function __construct(
        private PhotoTypeService $photoTypeService,
        private PhotoTypeProvider $photoTypeProvider,
        private PhotoTypeResponseMapper $photoTypeResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="PhotoType")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=PhotoTypeRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PhotoTypeDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(name: 'photo_types_update', methods: 'PUT')]
    #[ParamConverter(data: 'photoTypeRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $photoTypeId,
        PhotoTypeRequest $photoTypeRequest,
    ): Response {
        $photoType = $this->photoTypeProvider->get(Uuid::fromString($photoTypeId));

        $this->photoTypeService->update($photoType, $photoTypeRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->photoTypeResponseMapper->map($photoType));
    }
}
