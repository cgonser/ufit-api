<?php

namespace App\Customer\Entity;

use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\CustomerMeasurementItemRepository")
 * @ORM\Table(name="customer_measurement_item")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class CustomerMeasurementItem
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="CustomerMeasurement")
     * @ORM\JoinColumn(name="customer_measurement_id", referencedColumnName="id", nullable=false)
     */
    private CustomerMeasurement $customerMeasurement;

    /**
     * @ORM\ManyToOne(targetEntity="MeasurementType")
     * @ORM\JoinColumn(name="measurement_type_id", referencedColumnName="id", nullable=false)
     */
    private MeasurementType $measurementType;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"precision": 11, "scale": 2})
     */
    private string $measurement;

    /**
     * @ORM\Column()
     */
    private string $unit;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCustomerMeasurement(): CustomerMeasurement
    {
        return $this->customerMeasurement;
    }

    public function setCustomerMeasurement(CustomerMeasurement $customerMeasurement): self
    {
        $this->customerMeasurement = $customerMeasurement;

        return $this;
    }

    public function getMeasurementType(): MeasurementType
    {
        return $this->measurementType;
    }

    public function setMeasurementType(MeasurementType $measurementType): self
    {
        $this->measurementType = $measurementType;

        return $this;
    }

    public function getMeasurement(): Decimal
    {
        return new Decimal($this->measurement);
    }

    public function setMeasurement(Decimal $measurement): self
    {
        $this->measurement = $measurement;

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
}
