<?php

declare(strict_types=1);

namespace App\Customer\DataFixtures;

use App\Customer\Request\MeasurementTypeRequest;
use App\Customer\Service\MeasurementTypeService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MeasurementTypeFixtures extends Fixture
{
    private MeasurementTypeService $service;

    public function __construct(MeasurementTypeService $measurementTypeService)
    {
        $this->service = $measurementTypeService;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $request) {
            $obj = $this->service->create($request);

            $this->addReference('measurement_type-'.$obj->getSlug(), $obj);
        }

        $manager->flush();
    }

    private function getData(): \Iterator
    {
        $request = new MeasurementTypeRequest();
        $request->name = 'Weight';
        $request->units = ['kg', 'lb'];

        yield $request;

        $request = new MeasurementTypeRequest();
        $request->name = 'Height';
        $request->units = ['cm', 'ft'];

        yield $request;
    }
}
