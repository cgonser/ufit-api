<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorInvalidPhotoException;
use App\Vendor\Request\VendorPhotoRequest;
use App\Vendor\Service\VendorPhotoService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorPhotoController extends AbstractController
{
    private VendorPhotoService $vendorPhotoService;

    public function __construct(
        VendorPhotoService $vendorPhotoService
    ) {
        $this->vendorPhotoService = $vendorPhotoService;
    }

    /**
     * @Route("/vendors/{vendorId}/photo", methods="PUT", name="vendors_photo_upload")
     *
     * @ParamConverter("vendorPhotoRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=VendorPhotoRequest::class))
     * )
     * @OA\Response(
     *     response=204,
     *     description="Uploads a new photo"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        string $vendorId,
        VendorPhotoRequest $vendorPhotoRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            if ('current' == $vendorId) {
                /** @var Vendor $vendor */
                $vendor = $this->getUser();
            } else {
                // vendor fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $this->vendorPhotoService->uploadFromRequest($vendor, $vendorPhotoRequest);

            return new ApiJsonResponse(
                Response::HTTP_NO_CONTENT
            );
        } catch (VendorInvalidPhotoException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
