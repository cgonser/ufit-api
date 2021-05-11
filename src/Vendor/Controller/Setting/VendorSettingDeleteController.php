<?php

namespace App\Vendor\Controller\Setting;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Provider\VendorSettingProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorSettingManager;
use App\Vendor\Entity\Vendor;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorSettingDeleteController extends AbstractController
{
    private VendorSettingManager $vendorSettingManager;

    private VendorSettingProvider $vendorSettingProvider;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorSettingManager $vendorSettingManager,
        VendorSettingProvider $vendorSettingProvider,
        VendorProvider $vendorProvider
    ) {
        $this->vendorSettingManager = $vendorSettingManager;
        $this->vendorSettingProvider = $vendorSettingProvider;
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/settings/{vendorSettingId}",
     *     methods="DELETE",
     *     name="vendor_settings_delete"
     * )
     *
     * @OA\Tag(name="Vendor / Settings")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function delete(string $vendorId, string $vendorSettingId): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorSetting = $this->vendorSettingProvider->getByVendorAndId(
            $vendor->getId(),
            Uuid::fromString($vendorSettingId)
        );

        $this->vendorSettingManager->delete($vendorSetting);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
