<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Request\VendorUpdateRequest;
use App\Vendor\ResponseMapper\VendorResponseMapper;
use App\Vendor\Service\VendorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorUpdateController extends AbstractController
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
     * @Route("/vendors/{vendorId}", methods="PUT", name="vendors_update")
     *
     * @ParamConverter("vendorUpdateRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=VendorUpdateRequest::class))
     * )
     * @OA\Response(
     *     response=204,
     *     description="Updates a vendor",
     *     @OA\JsonContent(ref=@Model(type=VendorDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function update(
        string $vendorId,
        VendorUpdateRequest $vendorUpdateRequest,
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

            $this->vendorService->update($vendor, $vendorUpdateRequest);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (VendorNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (VendorEmailAddressInUseException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
