<?php

namespace App\Vendor\Service;

use App\Core\Service\EmailComposer;
use App\Vendor\Entity\Vendor;
use Symfony\Component\Mailer\MailerInterface;

class VendorEmailManager
{
    private EmailComposer $emailComposer;

    private MailerInterface $mailer;

    public function __construct(EmailComposer $emailComposer, MailerInterface $mailer)
    {
        $this->emailComposer = $emailComposer;
        $this->mailer = $mailer;
    }

    public function sendCreatedEmail(Vendor $vendor)
    {
        $this->mailer->send(
            $this->emailComposer->compose(
                'vendor.created',
                [
                    $vendor->getName() => $vendor->getEmail(),
                ],
                [
                    'greeting_name' => $vendor->getName(),
                ],
                $vendor->getLocale()
            )
        );
    }
}