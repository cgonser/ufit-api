<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorDto;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use App\Vendor\Request\VendorRequest;
use App\Vendor\ResponseMapper\VendorResponseMapper;
use App\Vendor\Service\VendorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorCreateController extends AbstractController
{
    private VendorService $vendorService;

    private VendorResponseMapper $vendorResponseMapper;

    public function __construct(
        VendorService $vendorService,
        VendorResponseMapper $vendorResponseMapper
    ) {
        $this->vendorService = $vendorService;
        $this->vendorResponseMapper = $vendorResponseMapper;
    }

    /**
     * @Route("/vendors", methods="POST", name="vendors_create")
     *
     * @ParamConverter("vendorRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=VendorRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new vendor",
     *     @OA\JsonContent(ref=@Model(type=VendorDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        VendorRequest $vendorRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $vendor = $this->vendorService->create($vendorRequest);

            return new ApiJsonResponse(Response::HTTP_CREATED, $this->vendorResponseMapper->map($vendor));
        } catch (VendorEmailAddressInUseException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
