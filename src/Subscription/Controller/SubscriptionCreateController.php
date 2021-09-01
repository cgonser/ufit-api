<?php

declare(strict_types=1);

namespace App\Subscription\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\ResponseMapper\InvoiceResponseMapper;
use App\Subscription\Dto\SubscriptionCreateDto;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SubscriptionCreateController extends AbstractController
{
    public function __construct(
        private SubscriptionRequestManager $subscriptionRequestManager,
        private SubscriptionResponseMapper $subscriptionResponseMapper,
        private InvoiceProvider $invoiceProvider,
        private InvoiceResponseMapper $invoiceResponseMapper
    ) {
    }

    /**
     *
     * @OA\Tag(name="Subscription")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=SubscriptionRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=SubscriptionCreateDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/subscriptions', name: 'subscriptions_create', methods: 'POST')]
    #[ParamConverter(
        data: 'subscriptionRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function subscribe(
        SubscriptionRequest $subscriptionRequest,
    ): ApiJsonResponse {
        $subscription = $this->subscriptionRequestManager->createFromRequest($subscriptionRequest);
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
