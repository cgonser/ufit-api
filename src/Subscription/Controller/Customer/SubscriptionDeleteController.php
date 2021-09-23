<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Service\SubscriptionManager;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionDeleteController extends AbstractController
{
    public function __construct(
        private SubscriptionProvider $subscriptionProvider,
        private SubscriptionManager $subscriptionManager,
        private CustomerProvider $customerProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Subscription")
     * @OA\Response(response=204, description="Cancels a subscription")
     * @OA\Response(response=404, description="Subscription not found")
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/customers/{customerId}/subscriptions/{subscriptionId}',
        name: 'customers_subscriptions_delete',
        methods: 'DELETE'
    )]
    public function cancelSubscription(string $customerId, string $subscriptionId): Response
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $subscription = $this->subscriptionProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($subscriptionId)
        );

        $this->subscriptionManager->customerCancellation($subscription);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
