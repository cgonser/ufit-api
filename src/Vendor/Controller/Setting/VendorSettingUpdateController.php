<?php

namespace App\Vendor\Controller\Setting;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorSettingDto;
use App\Vendor\Provider\VendorSettingProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorSettingRequest;
use App\Vendor\ResponseMapper\VendorSettingResponseMapper;
use App\Vendor\Service\VendorSettingRequestManager;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorSettingUpdateController extends AbstractController
{
    private VendorSettingRequestManager $vendorSettingManager;

    private VendorSettingProvider $vendorSettingProvider;

    private VendorSettingResponseMapper $vendorSettingResponseMapper;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorSettingRequestManager $vendorSettingManager,
        VendorSettingProvider $vendorSettingProvider,
        VendorSettingResponseMapper $vendorSettingResponseMapper,
        VendorProvider $vendorProvider
    ) {
        $this->vendorSettingManager = $vendorSettingManager;
        $this->vendorSettingProvider = $vendorSettingProvider;
        $this->vendorProvider = $vendorProvider;
        $this->vendorSettingResponseMapper = $vendorSettingResponseMapper;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/settings/{vendorSettingId}",
     *     methods="PUT",
     *     name="vendor_settings_update"
     * )
     * @ParamConverter("vendorSettingRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor / Settings")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorSettingRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=VendorSettingDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function update(
        string $vendorId,
        string $vendorSettingId,
        VendorSettingRequest $vendorSettingRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorSetting = $this->vendorSettingProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($vendorSettingId)
        );

        $vendorSettingRequest->vendorId = $vendor->getId();
        $this->vendorSettingManager->updateFromRequest($vendorSetting, $vendorSettingRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorSettingResponseMapper->map($vendorSetting)
        );
    }
}
