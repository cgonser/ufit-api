<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Request\VendorRequest;
use App\Vendor\ResponseMapper\VendorResponseMapper;
use App\Vendor\Service\VendorRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorUpdateController extends AbstractController
{
    private VendorRequestManager $vendorRequestManager;

    private VendorResponseMapper $vendorResponseMapper;

    public function __construct(
        VendorRequestManager $vendorRequestManager,
        VendorResponseMapper $vendorResponseMapper
    ) {
        $this->vendorRequestManager = $vendorRequestManager;
        $this->vendorResponseMapper = $vendorResponseMapper;
    }

    /**
     * @Route("/vendors/{vendorId}", methods="PUT", name="vendors_update")
     *
     * @ParamConverter("vendorRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorRequest::class)))
     * @OA\Response(response=200, description="Updates a vendor", @OA\JsonContent(ref=@Model(type=VendorDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function update(
        string $vendorId,
        VendorRequest $vendorRequest,
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

        $this->vendorRequestManager->updateFromRequest($vendor, $vendorRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorResponseMapper->map($vendor));
    }
}
