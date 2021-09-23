<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Core\Service\EmailComposer;
use App\Vendor\Entity\Vendor;
use DateTime;
use Symfony\Component\Mailer\MailerInterface;

class VendorEmailManager
{
    public function __construct(
        private VendorManager $vendorManager,
        private EmailComposer $emailComposer,
        private MailerInterface $mailer,
    ) {
    }

    public function sendCreatedEmail(Vendor $vendor): void
    {
        $this->mailer->send(
            $this->emailComposer->compose(
                'vendor.created',
                [
                    $vendor->getName() => $vendor->getEmail(),
                ],
                [
                    'greeting_name' => $vendor->getName() ?: $vendor->getDisplayName(),
                ],
                $vendor->getLocale()
            )
        );

        $vendor->setWelcomeEmailSentAt(new DateTime());

        $this->vendorManager->save($vendor);
    }
}
