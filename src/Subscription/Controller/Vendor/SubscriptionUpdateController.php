<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Provider\VendorSubscriptionProvider;
use App\Subscription\Request\SubscriptionReviewRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionRequestManager;
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

class SubscriptionUpdateController extends AbstractController
{
    public function __construct(
        private VendorSubscriptionProvider $vendorSubscriptionProvider,
        private SubscriptionRequestManager $subscriptionRequestManager,
        private SubscriptionResponseMapper $subscriptionResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Subscription")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=SubscriptionReviewRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Review (approve or reject) a subscription",
     *     @OA\JsonContent(ref=@Model(type=SubscriptionDto::class))
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/vendors/{vendorId}/subscriptions/{subscriptionId}/reviews',
        name: 'vendors_subscriptions_reviews_post',
        methods: 'POST'
    )]
    #[ParamConverter(
        data: 'subscriptionReviewRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function reviewSubscription(
        string $vendorId,
        string $subscriptionId,
        SubscriptionReviewRequest $subscriptionReviewRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $subscription = $this->vendorSubscriptionProvider->getByVendorAndId($vendor, Uuid::fromString($subscriptionId));
        $this->subscriptionRequestManager->review($subscription, $subscriptionReviewRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->subscriptionResponseMapper->map($subscription));
    }
}
