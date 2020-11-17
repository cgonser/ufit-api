<?php

namespace App\Subscription\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Exception\CustomerNotFoundException;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionService;
use App\Vendor\Exception\VendorPlanNotFoundException;
use App\Vendor\Provider\VendorPlanProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionCreateController extends AbstractController
{
    private VendorPlanProvider $vendorPlanProvider;

    private SubscriptionService $subscriptionService;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    public function __construct(
        VendorPlanProvider $vendorPlanProvider,
        SubscriptionService $subscriptionService,
        SubscriptionResponseMapper $subscriptionResponseMapper
    ) {
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->subscriptionService = $subscriptionService;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
    }

    /**
     * @Route("/subscriptions", methods="POST", name="subscription_create")
     *
     * @ParamConverter("subscriptionRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Subscription")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=SubscriptionRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a subscription",
     *     @OA\JsonContent(ref=@Model(type=SubscriptionDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function subscribe(SubscriptionRequest $subscriptionRequest)
    {
        try {
            $subscription = $this->subscriptionService->create($subscriptionRequest);

            return new ApiJsonResponse(Response::HTTP_CREATED, $this->subscriptionResponseMapper->map($subscription));
        } catch (VendorPlanNotFoundException | CustomerNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
