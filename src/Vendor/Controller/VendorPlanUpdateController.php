<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Request\VendorPlanUpdateRequest;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;
use App\Vendor\Service\VendorPlanService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorPlanUpdateController extends AbstractController
{
    private VendorPlanProvider $vendorPlanProvider;

    private VendorPlanService $vendorPlanService;

    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    public function __construct(
        VendorPlanProvider $vendorPlanProvider,
        VendorPlanService $vendorPlanService,
        VendorPlanResponseMapper $vendorPlanResponseMapper
    ) {
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->vendorPlanService = $vendorPlanService;
    }

    /**
     * @Route("/vendors/{vendorId}/plans/{vendorPlanId}", methods="PUT", name="vendors_plans_update")
     *
     * @ParamConverter("vendorPlanUpdateRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="VendorPlan")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=VendorPlanUpdateRequest::class))
     * )
     * @OA\Response(
     *     response=204,
     *     description="Updates a plan",
     *     @OA\JsonContent(ref=@Model(type=VendorPlanDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        string $vendorId,
        string $vendorPlanId,
        VendorPlanUpdateRequest $vendorPlanUpdateRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
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

            $vendorPlan = $this->vendorPlanProvider->getByVendorAndId($vendor, Uuid::fromString($vendorPlanId));

            $this->vendorPlanService->update($vendorPlan, $vendorPlanUpdateRequest);

            return new ApiJsonResponse(Response::HTTP_CREATED, $this->vendorPlanResponseMapper->map($vendorPlan));
        } catch (VendorPlanNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (VendorPlanInvalidDurationException | CurrencyNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
