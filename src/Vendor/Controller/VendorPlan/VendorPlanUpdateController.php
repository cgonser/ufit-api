<?php

declare(strict_types=1);

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
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

#[Route(path: '/vendors/{vendorId}/plans')]
class VendorPlanUpdateController extends AbstractController
{
    public function __construct(
        private VendorPlanProvider $vendorPlanProvider,
        private VendorPlanRequestManager $vendorPlanRequestManager,
        private VendorPlanResponseMapper $vendorPlanResponseMapper,
        private VendorPlanImageManager $vendorPlanImageManager,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Plan")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorPlanRequest::class)))
     * @OA\Response(response=200, description="Updates a plan", @OA\JsonContent(ref=@Model(type=VendorPlanDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Plan not found")
     */
    #[Route(path: '/{vendorPlanId}', name: 'vendors_plans_update', methods: ['PUT', 'PATCH'])]
    #[ParamConverter(data: 'vendorPlanRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function create(
        string $vendorId,
        string $vendorPlanId,
        VendorPlanRequest $vendorPlanRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }

        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));
        $this->vendorPlanRequestManager->updateFromRequest($vendorPlan, $vendorPlanRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorPlanResponseMapper->map($vendorPlan));
    }

    /**
     * @OA\Tag(name="Vendor / Plan")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=204, description="Uploads a photo")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/{vendorPlanId}/photo', name: 'vendors_plans_photo_update', methods: 'PUT')]
    public function upload(string $vendorId, string $vendorPlanId, Request $request): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));
        $this->vendorPlanImageManager->uploadPhoto($vendorPlan, $request->getContent());

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
