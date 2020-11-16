<?php

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\MeasureTypeRepository")
 * @ORM\Table(name="measure_type")
 * @UniqueEntity(fields={"name"})
 */
class MeasureType
{
    public const MEASURE_UNIT_KG = 'kg';
    public const MEASURE_UNIT_LB = 'lb';
    public const MEASURE_UNIT_CM = 'cm';
    public const MEASURE_UNIT_IN = 'in';

    public const MEASURE_TYPE_WEIGHT = 'weight';
    public const MEASURE_TYPE_SIZE = 'size';

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
    private string $unit;

    /**
     * @ORM\Column()
     */
    private string $type;

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

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}