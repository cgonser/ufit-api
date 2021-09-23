<?php

declare(strict_types=1);

namespace App\Subscription\Service;

use App\Core\Service\EmailComposer;
use App\Subscription\Entity\Subscription;
use Symfony\Component\Intl\Timezones;
use Symfony\Component\Mailer\MailerInterface;

class SubscriptionEmailManager
{
    public function __construct(private EmailComposer $emailComposer, private MailerInterface $mailer)
    {
    }

    public function sendCreatedEmail(Subscription $subscription): void
    {
        $customer = $subscription->getCustomer();
        $vendorPlan = $subscription->getVendorPlan();
        $vendor = $vendorPlan->getVendor();

//        $this->mailer->send(
//            $this->emailComposer->compose(
//                'subscription.created_customer',
//                [
//                    $customer->getName() => $customer->getEmail(),
//                ],
//                [
//                    'greeting_name' => $customer->getName(),
//                    'vendor_name' => $vendor->getDisplayName(),
//                ],
//                $customer->getLocale()
//            )
//        );

        $vendorTimezone = $vendor->getTimezone()
            ?: ($vendor->getCountry() ? Timezones::forCountryCode($vendor->getCountry())[0] : null)
                ?: null;

        $intlDateFormatter = new \IntlDateFormatter(
            $vendor->getLocale(),
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::SHORT,
            $vendorTimezone
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'subscription.created_vendor',
                [
                    $vendor->getName() => $vendor->getEmail(),
                ],
                [
                    'greeting_name' => $vendor->getName(),
                    'customer_name' => $customer->getName(),
                    'subscription_created_at' => $intlDateFormatter->format($subscription->getCreatedAt()),
                    'plan_name' => $vendorPlan->getName(),
                ],
                $vendor->getLocale()
            )
        );
    }

    public function sendApprovedEmail(Subscription $subscription): void
    {
        $customer = $subscription->getCustomer();
        $vendorPlan = $subscription->getVendorPlan();
        $vendor = $vendorPlan->getVendor();

        $this->mailer->send(
            $this->emailComposer->compose(
                'subscription.approved_vendor',
                [
                    $vendor->getName() => $vendor->getEmail(),
                ],
                [
                    'greeting_name' => $vendor->getName(),
                    'customer_name' => $customer->getName(),
                    'plan_name' => $vendorPlan->getName(),
                ],
                $vendor->getLocale()
            )
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'subscription.approved_customer',
                [
                    $customer->getName() => $customer->getEmail(),
                ],
                [
                    'greeting_name' => $customer->getName(),
                    'vendor_name' => $vendor->getName(),
                ],
                $customer->getLocale()
            )
        );
    }
}
