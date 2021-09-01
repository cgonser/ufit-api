<?php

declare(strict_types=1);

namespace App\Payment\Controller\Processor;

use Exception;
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
    public function __construct(private SubscriptionProvider $subscriptionProvider, private PaymentProvider $paymentProvider, private MessageBusInterface $messageBus, private LoggerInterface $logger)
    {
    }

    /**
     * @OA\Tag(name="Payment / Processor / Pagarme")
     */
    #[Route(path: '/payments/pagarme/postback', methods: 'POST', name: 'payments_pagarme_postback')]
    public function paymentPostback(Request $request) : Response
    {
        parse_str($request->getContent(), $payload);
        $this->logger->info('pagarme.postback', [
            'content' => $request->getContent(),
            'payload' => $payload,
        ]);
        try {
            $subscriptionId = $this->subscriptionProvider->get(Uuid::fromString($request->get('reference')))->getId();
        } catch (Exception) {
            $subscriptionId = null;
        }
        try {
            $paymentId = $this->paymentProvider->get(Uuid::fromString($request->get('reference')))->getId();
        } catch (Exception) {
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
