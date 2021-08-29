<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorPasswordChangeRequest;
use App\Vendor\Request\VendorPasswordResetRequest;
use App\Vendor\Request\VendorPasswordResetTokenRequest;
use App\Vendor\Service\VendorRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorPasswordController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorRequestManager $vendorRequestManager
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorPasswordChangeRequest::class)))
     * @OA\Response(response=204, description="Updates the current vendor's password")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/vendors/{vendorId}/password', name: 'vendor_password_change', methods: 'PUT')]
    #[ParamConverter(data: 'vendorPasswordChangeRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function changePassword(
        string $vendorId,
        VendorPasswordChangeRequest $vendorPasswordChangeRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }

        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $this->vendorRequestManager->changePassword($vendor, $vendorPasswordChangeRequest);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Tag(name="Vendor / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorPasswordResetRequest::class)))
     */
    #[Route(path: '/vendors/password-reset', name: 'vendor_password_reset', methods: 'POST')]
    #[ParamConverter(data: 'vendorPasswordResetRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function resetPassword(
        VendorPasswordResetRequest $vendorPasswordResetRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }
        $this->vendorRequestManager->startPasswordReset($vendorPasswordResetRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @OA\Tag(name="Vendor / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorPasswordResetTokenRequest::class)))
     */
    #[Route(path: '/vendors/password-reset/token', name: 'vendor_password_reset_token', methods: 'POST')]
    #[ParamConverter(data: 'vendorPasswordResetTokenRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function resetPasswordToken(
        VendorPasswordResetTokenRequest $vendorPasswordResetTokenRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }
        $this->vendorRequestManager->concludePasswordReset($vendorPasswordResetTokenRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @OA\Tag(name="Vendor / Demo")
     */
    #[Route(path: '/vendors/password-reset/{token}', name: 'vendor_password_reset_form', methods: 'GET')]
    public function resetPasswordForm(string $token): Response
    {
        return $this->render('vendor/password_reset.html.twig', [
            'token' => $token,
        ]);
    }
}
