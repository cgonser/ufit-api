<?php

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Request\SubscriptionReviewRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionRequestManager;
use App\Vendor\Entity\Vendor;
use App\Subscription\Provider\VendorSubscriptionProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionUpdateController extends AbstractController
{
    private VendorSubscriptionProvider $vendorSubscriptionProvider;

    private SubscriptionRequestManager $subscriptionManager;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    public function __construct(
        VendorSubscriptionProvider $vendorSubscriptionProvider,
        SubscriptionRequestManager $subscriptionManager,
        SubscriptionResponseMapper $subscriptionResponseMapper
    ) {
        $this->vendorSubscriptionProvider = $vendorSubscriptionProvider;
        $this->subscriptionManager = $subscriptionManager;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/subscriptions/{subscriptionId}/reviews",
     *     methods="POST",
     *     name="vendors_subscriptions_reviews_post"
     * )
     * @ParamConverter("subscriptionReviewRequest", converter="fos_rest.request_body")
     * @Security(name="Bearer")
     *
     *
     * @OA\Tag(name="Subscription")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=SubscriptionReviewRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Review (approve or reject) a subscription",
     *     @OA\JsonContent(ref=@Model(type=SubscriptionDto::class))
     * )
     */
    public function reviewSubscription(
        string $vendorId,
        string $subscriptionId,
        SubscriptionReviewRequest $subscriptionReviewRequest
    ): Response {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $subscription = $this->vendorSubscriptionProvider->getByVendorAndId($vendor, Uuid::fromString($subscriptionId));
        $this->subscriptionManager->review($subscription, $subscriptionReviewRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->subscriptionResponseMapper->map($subscription)
        );
    }
}
