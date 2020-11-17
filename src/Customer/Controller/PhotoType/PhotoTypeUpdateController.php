<?php

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

class PhotoTypeUpdateController extends AbstractController
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
     * @Route("/photo_types/{photoTypeId}", methods="PUT", name="photo_types_update")
     *
     * @ParamConverter("photoTypeRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="PhotoType")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=PhotoTypeRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a photo type",
     *     @OA\JsonContent(ref=@Model(type=PhotoTypeDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function update(
        string $photoTypeId,
        PhotoTypeRequest $photoTypeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $photoType = $this->photoTypeProvider->get(Uuid::fromString($photoTypeId));

            $this->photoTypeService->update($photoType, $photoTypeRequest);

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->photoTypeResponseMapper->map($photoType)
            );
        } catch (PhotoTypeNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (PhotoTypeAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
