<?php

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\CustomerMeasurementItemRepository")
 * @ORM\Table(name="customer_measurement_item")
 */
class CustomerMeasurementItem
{
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
     * @ORM\Column()
     */
    private ?int $measurement;

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

    public function getMeasurement(): ?int
    {
        return $this->measurement;
    }

    public function setMeasurement(?int $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }
}
