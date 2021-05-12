<?php

namespace App\Subscription\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\ResponseMapper\InvoiceResponseMapper;
use App\Subscription\Dto\SubscriptionDto;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Subscription\Service\SubscriptionRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SubscriptionCreateController extends AbstractController
{
    private SubscriptionRequestManager $subscriptionManager;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    private InvoiceProvider $invoiceProvider;

    private InvoiceResponseMapper $invoiceResponseMapper;

    public function __construct(
        SubscriptionRequestManager $subscriptionManager,
        SubscriptionResponseMapper $subscriptionResponseMapper,
        InvoiceProvider $invoiceProvider,
        InvoiceResponseMapper $invoiceResponseMapper
    ) {
        $this->subscriptionManager = $subscriptionManager;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
        $this->invoiceProvider = $invoiceProvider;
        $this->invoiceResponseMapper = $invoiceResponseMapper;
    }

    /**
     * @Route("/customers/{customerId}/subscriptions", methods="POST", name="customers_subscriptions_create")
     * @ParamConverter("subscriptionRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Subscription")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=SubscriptionRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a subscription for an existing customer",
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
        ConstraintViolationListInterface $validationErrors,
        Request $request
    ) {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();

            $subscriptionRequest->customerId = $customer->getId()->toString();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $subscription = $this->subscriptionManager->createFromCustomerRequest(
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
