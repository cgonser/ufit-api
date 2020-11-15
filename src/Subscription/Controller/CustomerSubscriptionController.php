<?php

namespace App\Subscription\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerSubscriptionController extends AbstractController
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
     * @Security(name="Bearer")
     */
    public function getCustomerSubscriptions(string $customerId): Response
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
}
