<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Core\Doctrine\Migrations\CoreMigration;
use App\Customer\Entity\MeasurementType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;

final class Version20201119020035 extends CoreMigration
{
    public function up(Schema $schema): void
    {
        $this->loadMeasurementType();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function loadMeasurementType(): void
    {
        foreach ($this->getMeasurementTypeData() as [$name, $slug, $units]) {
            $measurementType = new MeasurementType();
            $measurementType->setName($name);
            $measurementType->setSlug($slug);
            $measurementType->setUnits($units);

            $this->getService(EntityManagerInterface::class)->persist($measurementType);
        }

        $this->getService(EntityManagerInterface::class)->flush();
    }

    private function getMeasurementTypeData(): array
    {
        return [
            ['Weight', 'weight', 'kg,lb'],
            ['Height', 'height', 'cm,in'],
            ['Waist', 'waist', 'cm,in'],
            ['Chest', 'chest', 'cm,in'],
            ['Shoulders', 'shoulders', 'cm,in'],
            ['Glutes', 'glutes', 'cm,in'],
            ['Left arm', 'left-arm', 'cm,in'],
            ['Right arm', 'right-arm', 'cm,in'],
            ['Left forearm', 'left-forearm', 'cm,in'],
            ['Right forearm', 'right-forearm', 'cm,in'],
            ['Left thigh', 'left-thigh', 'cm,in'],
            ['Right thigh', 'right-thigh', 'cm,in'],
            ['Left calf', 'left-calf', 'cm,in'],
            ['Right calf', 'right-calf', 'cm,in'],
        ];
    }
}
