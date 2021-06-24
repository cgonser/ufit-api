<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Customer\Entity\Customer;
use App\Customer\Request\CustomerEmailChangeRequest;
use App\Customer\Service\CustomerRequestManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerEmailController extends AbstractController
{
    private CustomerRequestManager $customerManager;
    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    public function __construct(
        CustomerRequestManager $customerManager,
        AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
        $this->customerManager = $customerManager;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    /**
     * @Route("/customers/{customerId}/email", methods="PUT", name="customer_email_change")
     * @ParamConverter("customerEmailChangeRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer / E-mail")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerEmailChangeRequest::class)))
     * @OA\Response(response=204, description="Updates the current customer's email")
     * @OA\Response(response=400, description="Invalid input")
     */
    public function changeEmail(
        string $customerId,
        CustomerEmailChangeRequest $customerEmailChangeRequest,
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

        $this->customerManager->changeEmail($customer, $customerEmailChangeRequest);

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
    }
}
