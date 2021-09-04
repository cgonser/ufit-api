<?php

declare(strict_types=1);

namespace App\Subscription\Controller\Customer;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Provider\CustomerProvider;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\ResponseMapper\InvoiceResponseMapper;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionCreateController extends AbstractController
{
    public function __construct(
        private SubscriptionRequestManager $subscriptionRequestManager,
        private SubscriptionResponseMapper $subscriptionResponseMapper,
        private InvoiceProvider $invoiceProvider,
        private InvoiceResponseMapper $invoiceResponseMapper,
        private CustomerProvider $customerProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Subscription")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=SubscriptionRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=SubscriptionDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/customers/{customerId}/subscriptions', name: 'customers_subscriptions_create', methods: 'POST')]
    #[ParamConverter(
        data: 'subscriptionRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function subscribe(
        string $customerId,
        SubscriptionRequest $subscriptionRequest,
        Request $request
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $subscriptionRequest->customerId = $customer->getId()->toString();

        $subscription = $this->subscriptionRequestManager->createFromCustomerRequest(
            $customer,
            $subscriptionRequest,
            $request->getClientIp()
        );
        $invoice = $this->invoiceProvider->getSubscriptionNextDueInvoice($subscription->getId());

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            [
                'subscription' => $this->subscriptionResponseMapper->map($subscription),
                'invoice' => $this->invoiceResponseMapper->map($invoice),
            ]
        );
    }
}
