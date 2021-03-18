<?php

namespace App\Payment\Command;

use App\Customer\Entity\Customer;
use PagarMe\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaymentTestCommand extends Command
{
    protected static $defaultName = 'ufit:payment:test';

    private Client $pagarmeClient;

    public function __construct(
        Client $pagarmeClient
    ) {
        $this->pagarmeClient = $pagarmeClient;

        parent::__construct();
    }

    protected function configure()
    {
        $this
//            ->addArgument('subscriptionId')
            ->setDescription('Payment test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createTransaction();

        return 0;
    }

    protected function createTransaction()
    {
        $transaction = $this->pagarmeClient->transactions()->create([
            'amount' => 1000,
            'payment_method' => 'credit_card',
            'card_holder_name' => 'Anakin Skywalker',
            'card_number' => '4111111111111111',
            'card_cvv' => '123',
            'card_expiration_date' => '1221',
            'customer' => [
                'external_id' => '1',
                'name' => 'Ademir da Guia',
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                    [
                        'type' => 'cpf',
                        'number' => '33995835830',
                    ],
                ],
                'phone_numbers' => ['+551199999999'],
                'email' => 'cliente@email.com',
            ],
            'billing' => [
                'name' => 'Ademir da Guia',
                'address' => [
                    'country' => 'br',
                    'street' => 'Avenida Brigadeiro Faria Lima',
                    'street_number' => '1811',
                    'state' => 'sp',
                    'city' => 'Sao Paulo',
                    'neighborhood' => 'Jardim Paulistano',
                    'zipcode' => '01451001',
                ],
            ],
            'items' => [
                [
                    'id' => '1',
                    'title' => 'Subscription',
                    'unit_price' => 1000,
                    'quantity' => 1,
                    'tangible' => true,
                ],
            ],
        ]);

        dump($transaction);
    }
}
