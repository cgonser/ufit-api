<?php

declare(strict_types=1);

namespace App\Core\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;

abstract class CoreMigration extends AbstractMigration
{
    protected array $services = [];

    public function addService(string $serviceName, $service)
    {
        $this->services[$serviceName] = $service;
    }

    protected function getService(string $serviceName)
    {
        return $this->services[$serviceName];
    }
}
