<?php

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Request\SubscriptionReviewRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionService;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorSubscriptionProvider;
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

    private SubscriptionService $subscriptionService;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    public function __construct(
        VendorSubscriptionProvider $vendorSubscriptionProvider,
        SubscriptionService $subscriptionService,
        SubscriptionResponseMapper $subscriptionResponseMapper
    ) {
        $this->vendorSubscriptionProvider = $vendorSubscriptionProvider;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/subscriptions/{subscriptionId}/reviews",
     *     methods="POST",
     *     name="vendors_subscriptions_reviews_post"
     * )
     *
     * @ParamConverter("subscriptionReviewRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Subscription")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=SubscriptionReviewRequest::class))
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Review (approve or reject) a subscription",
     *     @OA\JsonContent(ref=@Model(type=SubscriptionDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function reviewSubscription(
        string $vendorId,
        string $subscriptionId,
        SubscriptionReviewRequest $subscriptionReviewRequest
    ): Response {
        try {
            if ('current' == $vendorId) {
                /** @var Vendor $vendor */
                $vendor = $this->getUser();
            } else {
                // vendor fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $subscription = $this->vendorSubscriptionProvider->getByVendorAndId(
                $vendor,
                Uuid::fromString($subscriptionId)
            );

            $this->subscriptionService->review($subscription, $subscriptionReviewRequest);

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->subscriptionResponseMapper->map($subscription)
            );
        } catch (SubscriptionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
