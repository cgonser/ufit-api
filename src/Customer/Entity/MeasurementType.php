<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\MeasurementTypeRepository")
 * @ORM\Table(name="measurement_type")
 * @UniqueEntity(fields={"name"})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class MeasurementType
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    public const UNIT_SEPARATOR = ',';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column()
     */
    private string $name;

    /**
     * @ORM\Column()
     */
    private string $slug;

    /**
     * @ORM\Column()
     */
    private string $units;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getUnits(): string
    {
        return $this->units;
    }

    public function setUnits(string $units): self
    {
        $this->units = $units;

        return $this;
    }

    public function isNew(): bool
    {
        return ! isset($this->id);
    }

    public function isUnitValid(string $unit): bool
    {
        return in_array($unit, explode(self::UNIT_SEPARATOR, $this->getUnits()), true);
    }
}
