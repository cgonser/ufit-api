<?php

declare(strict_types=1);

namespace App\Program\Service;

use App\Core\Service\EmailComposer;
use App\Program\Entity\ProgramAssignment;
use Symfony\Component\Mailer\MailerInterface;

class ProgramEmailManager
{
    private EmailComposer $emailComposer;

    private MailerInterface $mailer;

    public function __construct(EmailComposer $emailComposer, MailerInterface $mailer)
    {
        $this->emailComposer = $emailComposer;
        $this->mailer = $mailer;
    }

    public function sendAssignedEmail(ProgramAssignment $programAssignment): void
    {
        $customer = $programAssignment->getCustomer();
        $vendor = $programAssignment->getProgram()
            ->getVendor();

        $this->mailer->send(
            $this->emailComposer->compose(
                'customer.program_sent',
                [
                    $customer->getName() => $customer->getEmail(),
                ],
                [
                    'greeting_name' => $customer->getName(),
                    'vendor_name' => $vendor->getDisplayName(),
                    'download_url' => 'https://ufit.io',
                ],
                $customer->getLocale()
            )
        );
    }
}
