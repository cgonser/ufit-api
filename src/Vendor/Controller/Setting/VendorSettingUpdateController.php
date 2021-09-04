<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Setting;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorSettingDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Provider\VendorSettingProvider;
use App\Vendor\Request\VendorSettingRequest;
use App\Vendor\ResponseMapper\VendorSettingResponseMapper;
use App\Vendor\Service\VendorSettingRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/vendors/{vendorId}/settings')]
class VendorSettingUpdateController extends AbstractController
{
    public function __construct(
        private VendorSettingRequestManager $vendorSettingRequestManager,
        private VendorSettingProvider $vendorSettingProvider,
        private VendorSettingResponseMapper $vendorSettingResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Settings")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorSettingRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=VendorSettingDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route(path: '/{vendorSettingId}', name: 'vendor_settings_update', methods: 'PUT')]
    #[ParamConverter(data: 'vendorSettingRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $vendorId,
        string $vendorSettingId,
        VendorSettingRequest $vendorSettingRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorSetting = $this->vendorSettingProvider->getByVendorAndId(
            $vendor->getId(),
            Uuid::fromString($vendorSettingId)
        );

        $vendorSettingRequest->vendorId = $vendor->getId()
            ->toString();
        $this->vendorSettingRequestManager->updateFromRequest($vendorSetting, $vendorSettingRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorSettingResponseMapper->map($vendorSetting));
    }
}
