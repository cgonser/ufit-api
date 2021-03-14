<?php

namespace App\Vendor\Controller\Setting;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorSettingDto;
use App\Vendor\Provider\VendorSettingProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorSettingSearchRequest;
use App\Vendor\ResponseMapper\VendorSettingResponseMapper;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorSettingController extends AbstractController
{
    private VendorSettingProvider $vendorSettingProvider;

    private VendorSettingResponseMapper $vendorSettingResponseMapper;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorSettingProvider $vendorSettingProvider,
        VendorSettingResponseMapper $vendorSettingResponseMapper,
        VendorProvider $vendorProvider
    ) {
        $this->vendorSettingProvider = $vendorSettingProvider;
        $this->vendorSettingResponseMapper = $vendorSettingResponseMapper;
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/settings", methods="GET", name="vendor_settings_find")
     * @ParamConverter("searchRequest", converter="querystring")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Vendor / Settings")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=VendorSettingDto::class))))
     * )
     */
    public function getVendorSettings(string $vendorId, VendorSettingSearchRequest $searchRequest): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $searchRequest->vendorId = $vendor->getId();

        $vendorSettings = $this->vendorSettingProvider->search($searchRequest);
        $count = $this->vendorSettingProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorSettingResponseMapper->mapMultiple($vendorSettings),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
