<?php

namespace App\Subscription\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Service\SubscriptionService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionDeleteController extends AbstractController
{
    private SubscriptionProvider $subscriptionProvider;

    private SubscriptionService $subscriptionService;

    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        SubscriptionService $subscriptionService
    ) {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @Route("/customers/{customerId}/subscriptions/{subscriptionId}", methods="DELETE", name="customers_subscriptions_delete")
     *
     * @OA\Tag(name="Subscription")
     * @OA\Response(
     *     response=204,
     *     description="Cancels a subscription"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Subscription not found"
     * )
     *
     * @Security(name="Bearer")
     */
    public function cancelSubscription(string $customerId, string $subscriptionId): Response
    {
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $subscription = $this->subscriptionProvider->getByCustomerAndId($customer, Uuid::fromString($subscriptionId));

            $this->subscriptionService->customerCancellation($subscription);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (SubscriptionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
