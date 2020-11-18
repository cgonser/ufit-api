<?php

namespace App\Subscription\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    private SubscriptionProvider $subscriptionProvider;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        SubscriptionResponseMapper $subscriptionResponseMapper
    ) {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
    }

    /**
     * @Route("/customers/{customerId}/subscriptions", methods="GET", name="customers_subscriptions_get")
     *
     * @OA\Tag(name="Subscription")
     * @OA\Response(
     *     response=200,
     *     description="Returns all the subscriptions for a given customer",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=SubscriptionDto::class))
     *     )
     * )
     *
     * @Security(name="Bearer")
     */
    public function getSubscriptions(string $customerId): Response
    {
        if ('current' == $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $subscriptions = $this->subscriptionProvider->findByCustomer($customer);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->subscriptionResponseMapper->mapMultiple($subscriptions)
        );
    }

    /**
     * @Route("/customers/{customerId}/subscriptions/{subscriptionId}", methods="GET", name="customers_subscriptions_get_one")
     *
     * @OA\Tag(name="Subscription")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a subscription",
     *     @OA\JsonContent(ref=@Model(type=SubscriptionDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getSubscription(string $customerId, string $subscriptionId): Response
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

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->subscriptionResponseMapper->map($subscription)
            );
        } catch (SubscriptionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
