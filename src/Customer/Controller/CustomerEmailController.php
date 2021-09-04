<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerEmailChangeRequest;
use App\Customer\Service\CustomerRequestManager;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerEmailController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerRequestManager $customerRequestManager,
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
    ) {
    }

    /**
     * @OA\Tag(name="Customer / E-mail")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerEmailChangeRequest::class)))
     * @OA\Response(response=204, description="Updates the current customer's email")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/customers/{customerId}/email', name: 'customer_email_change', methods: 'PUT')]
    #[ParamConverter('customerEmailChangeRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function changeEmail(
        string $customerId,
        CustomerEmailChangeRequest $customerEmailChangeRequest,
    ): JWTAuthenticationSuccessResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $this->customerRequestManager->changeEmail($customer, $customerEmailChangeRequest);

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($customer);
    }
}
