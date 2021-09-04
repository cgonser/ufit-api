<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerPasswordChangeRequest;
use App\Customer\Request\CustomerPasswordResetRequest;
use App\Customer\Request\CustomerPasswordResetTokenRequest;
use App\Customer\Service\CustomerRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerPasswordController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerRequestManager $customerRequestManager,
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerPasswordChangeRequest::class)))
     * @OA\Response(response=204, description="Updates the current customer's password")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/customers/{customerId}/password', name: 'customer_password_change', methods: 'PUT')]
    #[ParamConverter(data: 'customerPasswordChangeRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function changePassword(
        string $customerId,
        CustomerPasswordChangeRequest $customerPasswordChangeRequest,
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $this->customerRequestManager->changePassword($customer, $customerPasswordChangeRequest);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Tag(name="Customer / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerPasswordResetRequest::class)))
     * @OA\Response(response=200, description="Password change requested")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/customers/password-reset', name: 'customer_password_reset', methods: 'POST')]
    #[ParamConverter(data: 'customerPasswordResetRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function resetPassword(
        CustomerPasswordResetRequest $customerPasswordResetRequest,
    ): ApiJsonResponse {
        $this->customerRequestManager->startPasswordReset($customerPasswordResetRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @OA\Tag(name="Customer / Password")
     */
    #[Route(path: '/customers/password-reset/token', name: 'customer_password_reset_token', methods: 'POST')]
    #[ParamConverter(data: 'customerPasswordResetTokenRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function resetPasswordToken(
        CustomerPasswordResetTokenRequest $customerPasswordResetTokenRequest,
    ): ApiJsonResponse {
        $this->customerRequestManager->concludePasswordReset($customerPasswordResetTokenRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @OA\Tag(name="Customer / Demo")
     */
    #[Route(path: '/customers/password-reset/{token}', name: 'customer_password_reset_form', methods: 'GET')]
    public function resetPasswordForm(string $token): Response
    {
        return $this->render('customer/password_reset.html.twig', [
            'token' => $token,
        ]);
    }
}
