<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Request\SubscriptionSearchRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Vendor\Entity\Vendor;
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
     * @Route("/vendors/{vendorId}/subscriptions", methods="GET", name="vendors_subscriptions_get")
     * @ParamConverter("subscriptionSearchRequest", converter="querystring")
     *
     * @OA\Tag(name="Subscription")
     * @OA\Parameter(
     *     in="query",
     *     name="filters",
     *     @OA\Schema(ref=@Model(type=SubscriptionSearchRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all subscriptions of a given vendor",
     *     @OA\Header(
     *         header="X-Total-Count",
     *         @OA\Schema(
     *             type="int"
     *         )
     *     ),
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=SubscriptionDto::class)))
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function getSubscriptions(
        string $vendorId,
        SubscriptionSearchRequest $subscriptionSearchRequest
    ): Response {
        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $subscriptionSearchRequest->vendorId = $vendor->getId();
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
     * @Route("/vendors/{vendorId}/subscriptions/{subscriptionId}", methods="GET", name="vendors_subscriptions_get_one")
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
    public function getSubscription(string $vendorId, string $subscriptionId): Response
    {
        try {
            if ('current' === $vendorId) {
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

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->subscriptionResponseMapper->map($subscription, true)
            );
        } catch (SubscriptionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
