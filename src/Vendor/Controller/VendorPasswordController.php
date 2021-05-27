<?php

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Entity\Vendor;
use App\Vendor\Request\VendorPasswordChangeRequest;
use App\Vendor\Request\VendorPasswordResetRequest;
use App\Vendor\Request\VendorPasswordResetTokenRequest;
use App\Vendor\Service\VendorRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorPasswordController extends AbstractController
{
    private VendorRequestManager $vendorManager;

    public function __construct(VendorRequestManager $vendorManager)
    {
        $this->vendorManager = $vendorManager;
    }

    /**
     * @Route("/vendors/{vendorId}/password", methods="PUT", name="vendor_password_change")
     * @ParamConverter("vendorPasswordChangeRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorPasswordChangeRequest::class)))
     * @OA\Response(response=204, description="Updates the current vendor's password")
     * @OA\Response(response=400, description="Invalid input")
     */
    public function changePassword(
        string $vendorId,
        VendorPasswordChangeRequest $vendorPasswordChangeRequest,
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
            throw new AccessDeniedHttpException();
        }

        $this->vendorManager->changePassword($vendor, $vendorPasswordChangeRequest);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/vendors/password-reset", methods="POST", name="vendor_password_reset")
     * @ParamConverter("vendorPasswordResetRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor / Password")
     */
    public function resetPassword(
        VendorPasswordResetRequest $vendorPasswordResetRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $this->vendorManager->startPasswordReset($vendorPasswordResetRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @Route("/vendors/password-reset/token", methods="POST", name="vendor_password_reset_token")
     * @ParamConverter("vendorPasswordResetTokenRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Vendor / Password")
     */
    public function resetPasswordToken(
        VendorPasswordResetTokenRequest $vendorPasswordResetTokenRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $this->vendorManager->concludePasswordReset($vendorPasswordResetTokenRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @Route("/vendors/password-reset/{token}", methods="GET", name="vendor_password_reset_form")
     *
     * @OA\Tag(name="Vendor / Demo")
     */
    public function resetPasswordForm(string $token): Response
    {
        return $this->render(
            'vendor/password_reset.html.twig',
            [
                'token' => $token,
            ]
        );
    }
}
