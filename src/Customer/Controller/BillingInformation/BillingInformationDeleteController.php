<?php

declare(strict_types=1);

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use App\Customer\Provider\BillingInformationProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Service\BillingInformationManager;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/customers/{customerId}/billing_information')]
class BillingInformationDeleteController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private BillingInformationManager $billingInformationManager,
        private BillingInformationProvider $billingInformationProvider
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     * @Security(name="Bearer")
     */
    #[Route(path: '/{billingInformationId}', name: 'customer_billing_information_delete', methods: 'DELETE')]
    public function delete(string $customerId, string $billingInformationId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $billingInformation = $this->billingInformationProvider->getByCustomerAndId(
            $customer->getId(),
            Uuid::fromString($billingInformationId)
        );

        $this->billingInformationManager->delete($billingInformation);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
