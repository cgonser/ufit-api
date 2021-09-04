<?php

declare(strict_types=1);

namespace App\Customer\EventSubscriber;

use App\Customer\Entity\Customer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class CurrentCustomerRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public function onControllerRequest(ControllerEvent $controllerEvent): void
    {
        $customer = $this->security->getUser();
        $request = $controllerEvent->getRequest();
        
        if ('current' === $request->attributes->get('customerId')) {
            if (! $customer instanceof Customer) {
                throw new AccessDeniedHttpException();
            }

            $request->attributes->set('customerId', $customer->getId()->toString());
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onControllerRequest',
        ];
    }
}
