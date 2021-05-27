<?php

namespace App\Core\EventSubscriber;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonErrorResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionEventSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $event->setResponse(
            $this->prepareResponse($event->getThrowable())
        );
    }

    private function prepareResponse(\Throwable $e)
    {
        if ($e instanceof ApiJsonException) {
            return new ApiJsonErrorResponse(
                $e->getStatusCode(),
                $e->getMessage(),
                $e->getErrors()
            );
        }

        if ($e instanceof HttpException) {
            return new ApiJsonErrorResponse($e->getStatusCode(), $e->getMessage());
        }

        return new ApiJsonErrorResponse($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
