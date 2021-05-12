<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Entity\Vendor;
use App\Vendor\Request\VendorSocialLinkRequest;
use App\Vendor\Service\VendorRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorSocialLinkController extends AbstractController
{
    private VendorRequestManager $vendorRequestManager;

    public function __construct(VendorRequestManager $vendorRequestManager)
    {
        $this->vendorRequestManager = $vendorRequestManager;
    }

    /**
     * @Route("/vendors/{vendorId}/socialLinks", methods="PUT", name="vendors_social_links_put")
     * @ParamConverter("vendorSocialLinkRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorSocialLinkRequest::class)))
     * @OA\Response(response=204, description="Defines a social network link")
     * @OA\Response(response=400, description="Invalid input")
     */
    public function create(
        string $vendorId,
        VendorSocialLinkRequest $vendorSocialLinkRequest,
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

        $this->vendorRequestManager->updateSocialLink($vendor, $vendorSocialLinkRequest);

        return new ApiJsonResponse(
            Response::HTTP_NO_CONTENT
        );
    }
}
