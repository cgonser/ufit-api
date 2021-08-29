<?php

declare(strict_types=1);

namespace App\Core\EventSubscriber;

use App\Core\Exception\ApiJsonInputValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationEventSubscriber implements EventSubscriberInterface
{
    public function onControllerArguments($event): void
    {
        foreach ($event->getArguments() as $argument) {
            if (! $argument instanceof ConstraintViolationList) {
                continue;
            }

            if ($argument->count() > 0) {
                throw new ApiJsonInputValidationException($argument);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onControllerArguments',
        ];
    }
}
