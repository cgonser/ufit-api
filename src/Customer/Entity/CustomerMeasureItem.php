<?php

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\CustomerMeasureItemRepository")
 * @ORM\Table(name="customer_measure_item")
 */
class CustomerMeasureItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="CustomerMeasure")
     * @ORM\JoinColumn(name="customer_measure_id", referencedColumnName="id", nullable=false)
     */
    private CustomerMeasure $customerMeasure;

    /**
     * @ORM\ManyToOne(targetEntity="MeasureType")
     * @ORM\JoinColumn(name="measure_type_id", referencedColumnName="id", nullable=false)
     */
    private MeasureType $measureType;

    /**
     * @ORM\Column()
     */
    private ?int $measure;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCustomerMeasure(): CustomerMeasure
    {
        return $this->customerMeasure;
    }

    public function setCustomerMeasure(CustomerMeasure $customerMeasure): self
    {
        $this->customerMeasure = $customerMeasure;

        return $this;
    }

    public function getMeasureType(): MeasureType
    {
        return $this->measureType;
    }

    public function setMeasureType(MeasureType $measureType): self
    {
        $this->measureType = $measureType;

        return $this;
    }

    public function getMeasure(): ?int
    {
        return $this->measure;
    }

    public function setMeasure(?int $measure): self
    {
        $this->measure = $measure;

        return $this;
    }
}
