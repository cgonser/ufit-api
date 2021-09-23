<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Setting;

use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorSettingDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Provider\VendorSettingProvider;
use App\Vendor\Request\VendorSettingSearchRequest;
use App\Vendor\ResponseMapper\VendorSettingResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/settings')]
class VendorSettingController extends AbstractController
{
    public function __construct(
        private VendorSettingProvider $vendorSettingProvider,
        private VendorSettingResponseMapper $vendorSettingResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @Security(name="Bearer")
     * @OA\Tag(name="Vendor / Settings")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=VendorSettingDto::class))))
     * )
     */
    #[Route(name: 'vendor_settings_find', methods: 'GET')]
    #[ParamConverter('vendorSettingSearchRequest', converter: 'querystring')]
    public function getVendorSettings(
        string $vendorId,
        VendorSettingSearchRequest $vendorSettingSearchRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $vendorSettingSearchRequest->vendorId = $vendor->getId()
            ->toString();
        $vendorSettings = $this->vendorSettingProvider->search($vendorSettingSearchRequest);
        $count = $this->vendorSettingProvider->count($vendorSettingSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorSettingResponseMapper->mapMultiple($vendorSettings),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
