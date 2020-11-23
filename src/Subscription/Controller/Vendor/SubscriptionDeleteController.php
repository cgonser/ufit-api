<?php

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Subscription\Provider\VendorSubscriptionProvider;
use App\Vendor\Entity\Vendor;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Service\SubscriptionService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionDeleteController extends AbstractController
{
    private VendorSubscriptionProvider $subscriptionProvider;

    private SubscriptionService $subscriptionService;

    public function __construct(
        VendorSubscriptionProvider $subscriptionProvider,
        SubscriptionService $subscriptionService
    ) {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @Route("/vendors/{vendorId}/subscriptions/{subscriptionId}", methods="DELETE", name="vendors_subscriptions_delete")
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
    public function cancelSubscription(string $vendorId, string $subscriptionId): Response
    {
        try {
            if ('current' == $vendorId) {
                /** @var Vendor $vendor */
                $vendor = $this->getUser();
            } else {
                // vendor fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $subscription = $this->subscriptionProvider->getByVendorAndId($$vendor, Uuid::fromString($subscriptionId));

            $this->subscriptionService->vendorCancellation($subscription);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (SubscriptionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
