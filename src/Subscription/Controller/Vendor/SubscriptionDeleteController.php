<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Subscription\Provider\VendorSubscriptionProvider;
use App\Subscription\Service\SubscriptionManager;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionDeleteController extends AbstractController
{
    public function __construct(
        private VendorSubscriptionProvider $vendorSubscriptionProvider,
        private SubscriptionManager $subscriptionManager,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Subscription")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Subscription not found")
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/vendors/{vendorId}/subscriptions/{subscriptionId}',
        name: 'vendors_subscriptions_delete',
        methods: 'DELETE'
    )]
    public function cancelSubscription(string $vendorId, string $subscriptionId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $subscription = $this->vendorSubscriptionProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($subscriptionId)
        );
        $this->subscriptionManager->vendorCancellation($subscription);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
