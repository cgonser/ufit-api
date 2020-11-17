<?php

namespace App\Customer\Controller\PhotoType;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\PhotoTypeDto;
use App\Customer\Exception\PhotoTypeAlreadyExistsException;
use App\Customer\Request\PhotoTypeRequest;
use App\Customer\ResponseMapper\PhotoTypeResponseMapper;
use App\Customer\Service\PhotoTypeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PhotoTypeCreateController extends AbstractController
{
    private PhotoTypeService $photoTypeService;

    private PhotoTypeResponseMapper $photoTypeResponseMapper;

    public function __construct(
        PhotoTypeService $photoTypeService,
        PhotoTypeResponseMapper $photoTypeResponseMapper
    ) {
        $this->photoTypeService = $photoTypeService;
        $this->photoTypeResponseMapper = $photoTypeResponseMapper;
    }

    /**
     * @Route("/photo_types", methods="POST", name="photo_types_create")
     *
     * @ParamConverter("photoTypeRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="PhotoType")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=PhotoTypeRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new photo type",
     *     @OA\JsonContent(ref=@Model(type=PhotoTypeDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        PhotoTypeRequest $photoTypeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $photoType = $this->photoTypeService->create($photoTypeRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->photoTypeResponseMapper->map($photoType)
            );
        } catch (PhotoTypeAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
