<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Provider\VendorSubscriptionProvider;
use App\Subscription\Request\SubscriptionSearchRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    public function __construct(
        private SubscriptionProvider $subscriptionProvider,
        private SubscriptionResponseMapper $subscriptionResponseMapper,
        private VendorSubscriptionProvider $vendorSubscriptionProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Subscription")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SubscriptionSearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=SubscriptionDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/vendors/{vendorId}/subscriptions', name: 'vendors_subscriptions_get', methods: 'GET')]
    #[ParamConverter(data: 'subscriptionSearchRequest', converter: 'querystring')]
    public function getSubscriptions(
        string $vendorId,
        SubscriptionSearchRequest $subscriptionSearchRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $subscriptionSearchRequest->vendorId = $vendor->getId()->toString();
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
    #[Route(
        path: '/vendors/{vendorId}/subscriptions/{subscriptionId}',
        name: 'vendors_subscriptions_get_one',
        methods: 'GET'
    )]
    public function getSubscription(string $vendorId, string $subscriptionId): Response
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $subscription = $this->vendorSubscriptionProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($subscriptionId)
        );

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->subscriptionResponseMapper->map($subscription, true)
        );
    }
}
