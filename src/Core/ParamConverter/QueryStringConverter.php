<?php

declare(strict_types=1);

namespace App\Core\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class QueryStringConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();

        $reflectionClass = new \ReflectionClass($class);
        $object = $reflectionClass->newInstance();

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            if (! $request->query->has($propertyName)) {
                continue;
            }

            $value = $request->query->get($propertyName);

            $object->{$propertyName} = match ($property->getType()->getName()) {
                'boolean', 'bool' => (bool) $value,
                'integer', 'int' => (int) $value,
                default => $value,
            };
        }

        $request->attributes->set($name, $object);
    }

    public function supports(ParamConverter $configuration)
    {
        return 'querystring' === $configuration->getConverter();
    }
}
