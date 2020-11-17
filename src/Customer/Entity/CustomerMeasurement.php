<?php

namespace App\Customer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\CustomerMeasurementRepository")
 * @ORM\Table(name="customer_measurement")
 */
class CustomerMeasurement
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    private Customer $customer;

    /**
     * @ORM\OneToMany(targetEntity="CustomerMeasurementItem", mappedBy="customerMeasurement", cascade={"persist"})
     */
    private Collection $items;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $notes = null;

    /**
     * @ORM\Column(name="taken_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $takenAt = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function addItem(CustomerMeasurementItem $customerMeasurementItem): self
    {
        $customerMeasurementItem->setCustomerMeasurement($this);

        $this->items[] = $customerMeasurementItem;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getTakenAt(): ?\DateTimeInterface
    {
        return $this->takenAt;
    }

    public function setTakenAt(?\DateTimeInterface $takenAt = null): self
    {
        $this->takenAt = $takenAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (null === $this->getTakenAt()) {
            $this->setTakenAt(new \DateTime());
        }
    }
}