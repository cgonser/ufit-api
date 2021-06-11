<?php

namespace App\Subscription\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerDto;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Request\SubscriptionSearchRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionCustomersController extends AbstractController
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
     * @Route("/vendors/{vendorId}/customers", methods="GET", name="vendors_subscriptions_get_customers")
     * @ParamConverter("searchRequest", converter="querystring")
     *
     * @OA\Tag(name="Subscription")
     * @OA\Parameter(
     *     in="query",
     *     name="filters",
     *     @OA\Schema(ref=@Model(type=SubscriptionSearchRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all customers with subscriptions for a given vendor",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CustomerDto::class))))
     * )
     * @Security(name="Bearer")
     */
    public function getCustomers(string $vendorId, SubscriptionSearchRequest $searchRequest): Response
    {
        if ('current' === $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $searchRequest->vendorId = $vendor->getId();
        $customers = $this->subscriptionProvider->searchCustomers($searchRequest);
        $count = $this->subscriptionProvider->countCustomers($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->subscriptionResponseMapper->mapMultipleCustomers($customers),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
