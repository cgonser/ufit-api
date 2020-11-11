<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorInvalidPasswordException;
use App\Vendor\Request\VendorPasswordChangeRequest;
use App\Vendor\Service\VendorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorPasswordController extends AbstractController
{
    private VendorService $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * @Route("/vendors/{vendorId}/password", methods="PUT", name="vendor_password_change")
     *
     * @ParamConverter("vendorPasswordChangeRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=VendorPasswordChangeRequest::class))
     * )
     * @OA\Response(
     *     response=204,
     *     description="Updates the password of a vendor"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function changePassword(
        string $vendorId,
        VendorPasswordChangeRequest $vendorPasswordChangeRequest,
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

            $this->vendorService->changePassword($vendor, $vendorPasswordChangeRequest);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (VendorInvalidPasswordException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
