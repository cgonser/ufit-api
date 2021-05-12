<?php

namespace App\Vendor\Controller\VendorPlan;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Request\VendorPlanRequest;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;
use App\Vendor\Service\VendorPlanRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorPlanCreateController extends AbstractController
{
    private VendorPlanRequestManager $vendorPlanRequestManager;

    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    public function __construct(
        VendorPlanRequestManager $vendorPlanRequestManager,
        VendorPlanResponseMapper $vendorPlanResponseMapper
    ) {
        $this->vendorPlanRequestManager = $vendorPlanRequestManager;
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}/plans", methods="POST", name="vendors_plans_create")
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
     *     response=201,
     *     description="Creates a new plan",
     *     @OA\JsonContent(ref=@Model(type=VendorPlanDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        string $vendorId,
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

        $vendorPlan = $this->vendorPlanRequestManager->createFromRequest($vendor, $vendorPlanRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->vendorPlanResponseMapper->map($vendorPlan)
        );
    }
}
