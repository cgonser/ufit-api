<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Request\SubscriptionSearchRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/customers/{customerId}/subscriptions')]
class SubscriptionController extends AbstractController
{
    public function __construct(
        private SubscriptionProvider $subscriptionProvider,
        private SubscriptionResponseMapper $subscriptionResponseMapper,
        private CustomerProvider $customerProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Subscription")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SubscriptionSearchRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=SubscriptionDto::class))))
     * @Security(name="Bearer")
     */
    #[Route(name: 'customers_subscriptions_get', methods: 'GET')]
    #[ParamConverter(data: 'subscriptionSearchRequest', converter: 'querystring')]
    public function getSubscriptions(
        string $customerId,
        SubscriptionSearchRequest $subscriptionSearchRequest
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $subscriptionSearchRequest->customerId = $customer->getId()->toString();
        $subscriptions = $this->subscriptionProvider->search($subscriptionSearchRequest);
        $count = $this->subscriptionProvider->count($subscriptionSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->subscriptionResponseMapper->mapMultiple($subscriptions, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Subscription")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=SubscriptionDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/{subscriptionId}', name: 'customers_subscriptions_get_one', methods: 'GET')]
    public function getSubscription(string $customerId, string $subscriptionId): Response
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $subscription = $this->subscriptionProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($subscriptionId)
        );

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->subscriptionResponseMapper->map($subscription, true)
        );
    }
}
