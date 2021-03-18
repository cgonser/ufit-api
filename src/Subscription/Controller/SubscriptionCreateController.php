<?php

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
    private SubscriptionRequestManager $subscriptionService;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    private InvoiceProvider $invoiceProvider;

    private InvoiceResponseMapper $invoiceResponseMapper;

    public function __construct(
        SubscriptionRequestManager $subscriptionService,
        SubscriptionResponseMapper $subscriptionResponseMapper,
        InvoiceProvider $invoiceProvider,
        InvoiceResponseMapper $invoiceResponseMapper
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
        $this->invoiceProvider = $invoiceProvider;
        $this->invoiceResponseMapper = $invoiceResponseMapper;
    }

    /**
     * @Route("/subscriptions", methods="POST", name="subscriptions_create")
     *
     * @ParamConverter("subscriptionRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Subscription")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=SubscriptionRequest::class)))
     * @OA\Response(response=201,
     *     description="Creates a subscription for a new or existing customer",
     *     @OA\JsonContent(ref=@Model(type=SubscriptionCreateDto::class))
     * )
     * @OA\Response(response=400, description="Invalid input")
     */
    public function subscribe(
        SubscriptionRequest $subscriptionRequest,
        ConstraintViolationListInterface $validationErrors
    ) {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $subscription = $this->subscriptionService->createFromRequest($subscriptionRequest);

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
