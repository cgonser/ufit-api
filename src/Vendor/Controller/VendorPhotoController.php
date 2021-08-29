<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\ResponseMapper\VendorResponseMapper;
use App\Vendor\Service\VendorPhotoManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorPhotoController extends AbstractController
{
    private VendorPhotoManager $vendorPhotoManager;
    private VendorResponseMapper $vendorResponseMapper;

    public function __construct(
        VendorPhotoManager $vendorPhotoManager,
        VendorResponseMapper $vendorResponseMapper
    ) {
        $this->vendorPhotoManager = $vendorPhotoManager;
        $this->vendorResponseMapper = $vendorResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}/photo", methods="PUT", name="vendors_photo_upload")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=VendorDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function create(
        string $vendorId,
        Request $request
    ): Response {
        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $this->vendorPhotoManager->uploadPhoto($vendor, $request->getContent());

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorResponseMapper->map($vendor)
        );
    }
}
