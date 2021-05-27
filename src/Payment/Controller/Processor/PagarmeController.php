<?php

namespace App\Payment\Controller\Processor;

use App\Payment\Message\PagarmeSubscriptionResponseReceivedEvent;
use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class PagarmeController extends AbstractController
{
    private MessageBusInterface $messageBus;

    private LoggerInterface $logger;

    public function __construct(
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->messageBus = $messageBus;
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

        if ('subscription' === $payload['object']) {
            $this->messageBus->dispatch(new PagarmeSubscriptionResponseReceivedEvent((object) $payload['subscription']));
        }

        if ('transaction' === $payload['object']) {
            $this->messageBus->dispatch(new PagarmeTransactionResponseReceivedEvent((object) $payload['transaction']));
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
