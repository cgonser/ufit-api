<?php

namespace App\Vendor\Controller\Setting;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorSettingDto;
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

class VendorSettingCreateController extends AbstractController
{
    private VendorSettingResponseMapper $vendorSettingResponseMapper;

    private VendorSettingRequestManager $vendorSettingManager;

    public function __construct(
        VendorSettingResponseMapper $vendorSettingResponseMapper,
        VendorSettingRequestManager $vendorSettingManager
    ) {
        $this->vendorSettingResponseMapper = $vendorSettingResponseMapper;
        $this->vendorSettingManager = $vendorSettingManager;
    }

    /**
     * @Route("/vendors/{vendorId}/settings", methods="POST", name="vendor_settings_create")
     * @ParamConverter("vendorSettingRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor / Settings")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorSettingRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=VendorSettingDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function create(
        string $vendorId,
        VendorSettingRequest $vendorSettingRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorSettingRequest->vendorId = $vendor->getId();
        $vendorSetting = $this->vendorSettingManager->createFromRequest($vendorSettingRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->vendorSettingResponseMapper->map($vendorSetting)
        );
    }
}
