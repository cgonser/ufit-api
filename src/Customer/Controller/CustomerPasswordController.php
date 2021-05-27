<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Request\CustomerPasswordChangeRequest;
use App\Customer\Request\CustomerPasswordResetRequest;
use App\Customer\Request\CustomerPasswordResetTokenRequest;
use App\Customer\Service\CustomerRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerPasswordController extends AbstractController
{
    private CustomerRequestManager $customerManager;

    public function __construct(CustomerRequestManager $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    /**
     * @Route("/customers/{customerId}/password", methods="PUT", name="customer_password_change")
     * @ParamConverter("customerPasswordChangeRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerPasswordChangeRequest::class)))
     * @OA\Response(response=204, description="Updates the current customer's password")
     * @OA\Response(response=400, description="Invalid input")
     */
    public function changePassword(
        string $customerId,
        CustomerPasswordChangeRequest $customerPasswordChangeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new AccessDeniedHttpException();
        }

        $this->customerManager->changePassword($customer, $customerPasswordChangeRequest);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/customers/password-reset", methods="POST", name="customer_password_reset")
     * @ParamConverter("customerPasswordResetRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerPasswordResetRequest::class)))
     * @OA\Response(response=200, description="Password change requested")
     * @OA\Response(response=400, description="Invalid input")
     */
    public function resetPassword(
        CustomerPasswordResetRequest $customerPasswordResetRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $this->customerManager->startPasswordReset($customerPasswordResetRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @Route("/customers/password-reset/token", methods="POST", name="customer_password_reset_token")
     * @ParamConverter("customerPasswordResetTokenRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer / Password")
     */
    public function resetPasswordToken(
        CustomerPasswordResetTokenRequest $customerPasswordResetTokenRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $this->customerManager->concludePasswordReset($customerPasswordResetTokenRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @Route("/customers/password-reset/{token}", methods="GET", name="customer_password_reset_form")
     *
     * @OA\Tag(name="Customer / Demo")
     */
    public function resetPasswordForm(string $token): Response
    {
        return $this->render(
            'customer/password_reset.html.twig',
            [
                'token' => $token,
            ]
        );
    }
}
