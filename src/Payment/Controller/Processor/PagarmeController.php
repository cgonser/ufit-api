<?php

namespace App\Payment\Controller\Processor;

use App\Payment\Message\PagarmeSubscriptionResponseReceivedEvent;
use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Payment\Provider\PaymentProvider;
use App\Subscription\Provider\SubscriptionProvider;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class PagarmeController extends AbstractController
{
    private MessageBusInterface $messageBus;

    private LoggerInterface $logger;
    private SubscriptionProvider $subscriptionProvider;
    private PaymentProvider $paymentProvider;

    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        PaymentProvider $paymentProvider,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->messageBus = $messageBus;
        $this->subscriptionProvider = $subscriptionProvider;
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * @Route("/payments/pagarme/postback", methods="POST", name="payments_pagarme_postback")
     *
     * @OA\Tag(name="Payment / Processor / Pagarme")
     */
    public function paymentPostback(Request $request)
    {
        parse_str($request->getContent(), $payload);

        $this->logger->info(
            'pagarme.postback',
            [
                'content' => $request->getContent(),
                'payload' => $payload,
            ]
        );

        try {
            $subscriptionId = $this->subscriptionProvider->get(Uuid::fromString($request->get('reference')))->getId();
        } catch (\Exception $e) {
            $subscriptionId = null;
        }

        try {
            $paymentId = $this->paymentProvider->get(Uuid::fromString($request->get('reference')))->getId();
        } catch (\Exception $e) {
            $paymentId = null;
        }

        if ('subscription' === $payload['object']) {
            $this->messageBus->dispatch(
                new PagarmeSubscriptionResponseReceivedEvent(
                    (object) $payload['subscription'],
                    $subscriptionId,
                    $paymentId
                )
            );
        }

        if ('transaction' === $payload['object']) {
            $this->messageBus->dispatch(
                new PagarmeTransactionResponseReceivedEvent(
                    (object) $payload['transaction'],
                    $subscriptionId,
                    $paymentId
                )
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
