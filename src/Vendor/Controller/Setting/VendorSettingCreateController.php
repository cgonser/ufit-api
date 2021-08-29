<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Setting;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorSettingDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
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
class VendorSettingCreateController extends AbstractController
{
    public function __construct(
        private VendorSettingResponseMapper $vendorSettingResponseMapper,
        private VendorSettingRequestManager $vendorSettingRequestManager,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Settings")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorSettingRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=VendorSettingDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route(name: 'vendor_settings_create', methods: 'POST')]
    #[ParamConverter(data: 'vendorSettingRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function create(
        string $vendorId,
        VendorSettingRequest $vendorSettingRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorSettingRequest->vendorId = $vendor->getId()->toString();
        $vendorSetting = $this->vendorSettingRequestManager->createFromRequest($vendorSettingRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->vendorSettingResponseMapper->map($vendorSetting)
        );
    }
}
