<?php

namespace App\Subscription\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerNotFoundException;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionService;
use App\Vendor\Exception\VendorPlanNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SubscriptionCreateController extends AbstractController
{
    private SubscriptionService $subscriptionService;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    public function __construct(
        SubscriptionService $subscriptionService,
        SubscriptionResponseMapper $subscriptionResponseMapper
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
    }

    /**
     * @Route("/customers/{customerId}/subscriptions", methods="POST", name="customers_subscriptions_create")
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
    public function subscribe(
        string $customerId,
        SubscriptionRequest $subscriptionRequest,
        ConstraintViolationListInterface $validationErrors
    ) {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $subscription = $this->subscriptionService->createFromCustomerRequest($customer, $subscriptionRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->subscriptionResponseMapper->map($subscription)
            );
        } catch (VendorPlanNotFoundException | CustomerNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
