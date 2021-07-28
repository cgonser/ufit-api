<?php

namespace App\Customer\Service;

use App\Core\Service\EmailComposer;
use App\Customer\Entity\Customer;
use Symfony\Component\Mailer\MailerInterface;

class CustomerEmailManager
{
    private EmailComposer $emailComposer;

    private MailerInterface $mailer;

    public function __construct(EmailComposer $emailComposer, MailerInterface $mailer)
    {
        $this->emailComposer = $emailComposer;
        $this->mailer = $mailer;
    }

    public function sendCreatedEmail(Customer $customer): void
    {
        $this->mailer->send(
            $this->emailComposer->compose(
                'customer.created',
                [
                    $customer->getName() => $customer->getEmail(),
                ],
                [
                    'greeting_name' => $customer->getName(),
                ],
                $customer->getLocale()
            )
        );
    }
}