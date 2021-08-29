<?php

declare(strict_types=1);

namespace App\Vendor\EventSubscriber;

use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class CurrentVendorRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public function onControllerRequest(ControllerEvent $controllerEvent): void
    {
        $vendor = $this->security->getUser();
        $request = $controllerEvent->getRequest();

        if ('current' === $request->attributes->get('vendorSlug')) {
            if (!$vendor instanceof Vendor) {
                throw new AccessDeniedHttpException();
            }

            $request->attributes->set('vendorSlug', $vendor->getSlug());
        }

        if ('current' === $request->attributes->get('vendorId')) {
            if (!$vendor instanceof Vendor) {
                throw new AccessDeniedHttpException();
            }

            $request->attributes->set('vendorId', $vendor->getId()->toString());
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
