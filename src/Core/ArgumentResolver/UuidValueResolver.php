<?php

namespace App\Core\ArgumentResolver;

use Iterator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UuidValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_a($argument->getType(), UuidInterface::class, true);
    }

    /**
     * @return UuidInterface|null
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Iterator
    {
        $argumentValue = $request->get($argument->getName());
        if (! is_string($argumentValue)) {
            yield null;
        }

        yield Uuid::fromString($argumentValue);
    }
}