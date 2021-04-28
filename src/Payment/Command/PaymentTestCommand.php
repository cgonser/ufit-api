<?php

namespace App\Payment\Command;

use App\Payment\Request\PaymentRequest;
use App\Payment\Service\PaymentRequestManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaymentTestCommand extends Command
{
    protected static $defaultName = 'ufit:payment:test';

    private PaymentRequestManager $paymentRequestManager;

    public function __construct(
        PaymentRequestManager $paymentRequestManager
    ) {
        $this->paymentRequestManager = $paymentRequestManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('invoiceId')
            ->setDescription('Payment test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paymentRequest = new PaymentRequest();
        $paymentRequest->invoiceId = $input->getArgument('invoiceId');
        $paymentRequest->paymentMethodId = $paymentMethod->getId();
        $paymentRequest->details = $details;

        return 0;
    }
}
