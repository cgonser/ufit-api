<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\ResponseMapper\VendorResponseMapper;
use App\Vendor\Service\VendorPhotoManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorPhotoController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorPhotoManager $vendorPhotoManager,
        private VendorResponseMapper $vendorResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=VendorDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/vendors/{vendorId}/photo', name: 'vendors_photo_upload', methods: 'PUT')]
    public function create(string $vendorId, Request $request): ApiJsonResponse
    {
        $contentType = $request->headers->get('Content-Type');
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);
        $this->vendorPhotoManager->uploadPhoto($vendor, $request->getContent());

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorResponseMapper->map($vendor));
    }
}
