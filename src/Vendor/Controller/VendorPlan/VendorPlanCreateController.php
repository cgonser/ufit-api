<?php

declare(strict_types=1);

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorPlanRequest;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;
use App\Vendor\Service\VendorPlanRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/vendors/{vendorId}/plans')]
class VendorPlanCreateController extends AbstractController
{
    public function __construct(
        private VendorPlanRequestManager $vendorPlanRequestManager,
        private VendorPlanResponseMapper $vendorPlanResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Plan")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorPlanRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=VendorPlanDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(name: 'vendors_plans_create', methods: 'POST')]
    #[ParamConverter(data: 'vendorPlanRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function create(
        string $vendorId,
        VendorPlanRequest $vendorPlanRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }

        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorPlan = $this->vendorPlanRequestManager->createFromRequest($vendor, $vendorPlanRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->vendorPlanResponseMapper->map($vendorPlan));
    }
}
