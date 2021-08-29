<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Setting;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Provider\VendorSettingProvider;
use App\Vendor\Service\VendorSettingManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/settings')]
class VendorSettingDeleteController extends AbstractController
{
    public function __construct(
        private VendorSettingManager $vendorSettingManager,
        private VendorSettingProvider $vendorSettingProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Settings")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route(path: '/{vendorSettingId}', name: 'vendor_settings_delete', methods: 'DELETE')]
    public function delete(string $vendorId, string $vendorSettingId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $this->vendorSettingManager->delete(
            $this->vendorSettingProvider->getByVendorAndId(
                $vendor->getId(),
                Uuid::fromString($vendorSettingId)
            )
        );

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
