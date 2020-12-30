<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
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

class VendorController extends AbstractController
{
    private VendorProvider $vendorProvider;

    private VendorResponseMapper $vendorResponseMapper;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorResponseMapper $vendorResponseMapper
    ) {
        $this->vendorResponseMapper = $vendorResponseMapper;
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @Route("/vendors", methods="GET", name="vendors_get")
     *
     * @OA\Tag(name="Vendor")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about all vendors",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=VendorDto::class)))
     *     )*
     * )
     * @Security(name="Bearer")
     */
    public function getVendors(): Response
    {
        $vendors = $this->vendorProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorResponseMapper->mapMultiple($vendors)
        );
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}",
     *     methods="GET",
     *     name="vendors_get_one",
     *     requirements={"vendorId" = "[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}"}
     * )
     *
     * @OA\Tag(name="Vendor")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a vendor",
     *     @OA\JsonContent(ref=@Model(type=VendorDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getVendor(string $vendorId): Response
    {
        try {
            $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));

            return new ApiJsonResponse(Response::HTTP_OK, $this->vendorResponseMapper->map($vendor));
        } catch (VendorNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }

    /**
     * @Route("/vendors/{slug}", methods="GET", name="vendors_get_one_by_slug")
     *
     * @OA\Tag(name="Vendor")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a vendor",
     *     @OA\JsonContent(ref=@Model(type=VendorDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getVendorBySlug(string $slug): Response
    {
        try {
            if ('current' == $slug) {
                /** @var Vendor $vendor */
                $vendor = $this->getUser();
            } else {
                $vendor = $this->vendorProvider->findOneBySlug($slug);

                if (null === $vendor) {
                    throw new VendorNotFoundException();
                }
            }

            return new ApiJsonResponse(Response::HTTP_OK, $this->vendorResponseMapper->map($vendor));
        } catch (VendorNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
