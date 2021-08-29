<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\ResponseMapper\VendorResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VendorController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorResponseMapper $vendorResponseMapper
    ) {
    }

//    /**
//     *
//     * @OA\Tag(name="Vendor")
//     * @OA\Response(
//     *     response=200,
//     *     description="Returns the information about all vendors",
//     *     @OA\JsonContent(
//     *         type="array",
//     *         @OA\Items(ref=@Model(type=VendorDto::class)))
//     *     )*
//     * )
//     * @Security(name="Bearer")
//     */
//    #[Route(path: '/vendors', methods: 'GET', name: 'vendors_get')]
//    public function getVendors(): ApiJsonResponse
//    {
//        $vendors = $this->vendorProvider->findAll();
//
//        return new ApiJsonResponse(
//            Response::HTTP_OK,
//            $this->vendorResponseMapper->mapMultiple($vendors)
//        );
//    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a vendor",
     *     @OA\JsonContent(ref=@Model(type=VendorDto::class))
     * )
     */
    #[Route(
        path: '/vendors/{vendorId}',
        name: 'vendors_get_one',
        requirements: [
            'vendorId' => '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}',
        ],
        methods: ['GET']
    )]
    public function getVendor(string $vendorId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));

        if ($this->getUser() && $this->getUser()->getId()->equals($vendor->getId())) {
            return new ApiJsonResponse(Response::HTTP_OK, $this->vendorResponseMapper->map($vendor));
        }

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorResponseMapper->mapPublic($vendor));
    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a vendor",
     *     @OA\JsonContent(ref=@Model(type=VendorDto::class))
     * )
     */
    #[Route(path: '/vendors/{vendorSlug}', name: 'vendors_get_one_by_slug', methods: 'GET')]
    public function getVendorBySlug(string $vendorSlug): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->findOneBySlug($vendorSlug);

        if (! $vendor instanceof Vendor) {
            throw new VendorNotFoundException();
        }

        $vendorDto = $this->getUser() && $this->getUser()->getId()->equals($vendor->getId())
            ? $this->vendorResponseMapper->map($vendor)
            : $this->vendorResponseMapper->mapPublic($vendor);

        return new ApiJsonResponse(Response::HTTP_OK, $vendorDto);
    }
}
