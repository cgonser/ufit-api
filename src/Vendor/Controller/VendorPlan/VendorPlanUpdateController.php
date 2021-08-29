<?php

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Request\VendorPlanRequest;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;
use App\Vendor\Service\VendorPlanImageManager;
use App\Vendor\Service\VendorPlanRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorPlanUpdateController extends AbstractController
{
    private VendorPlanProvider $vendorPlanProvider;
    private VendorPlanRequestManager $vendorPlanRequestManager;
    private VendorPlanResponseMapper $vendorPlanResponseMapper;
    private VendorPlanImageManager $vendorPlanImageManager;

    public function __construct(
        VendorPlanProvider $vendorPlanProvider,
        VendorPlanRequestManager $vendorPlanRequestManager,
        VendorPlanResponseMapper $vendorPlanResponseMapper,
        VendorPlanImageManager $vendorPlanImageManager
    ) {
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->vendorPlanRequestManager = $vendorPlanRequestManager;
        $this->vendorPlanImageManager = $vendorPlanImageManager;
    }

    /**
     * @Route("/vendors/{vendorId}/plans/{vendorPlanId}", methods="PUT", name="vendors_plans_update")
     *
     * @ParamConverter("vendorPlanRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor / Plan")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=VendorPlanRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a plan",
     *     @OA\JsonContent(ref=@Model(type=VendorPlanDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Plan not found"
     * )
     */
    public function create(
        string $vendorId,
        string $vendorPlanId,
        VendorPlanRequest $vendorPlanRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));

        $this->vendorPlanRequestManager->updateFromRequest($vendorPlan, $vendorPlanRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorPlanResponseMapper->map($vendorPlan)
        );
    }

    /**
     * @Route("/vendors/{vendorId}/plans/{vendorPlanId}/photo", methods="PUT", name="vendors_plans_photo_update")
     *
     * @OA\Tag(name="Vendor / Plan")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=204, description="Uploads a photo")
     * @OA\Response(response=400, description="Invalid input")
     */
    public function upload(
        string $vendorId,
        string $vendorPlanId,
        Request $request
    ): Response {
        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));

        $this->vendorPlanImageManager->uploadPhoto($vendorPlan, $request->getContent());

        return new ApiJsonResponse(
            Response::HTTP_NO_CONTENT
        );
    }
}
