<?php

namespace App\Subscription\Entity;

use App\Customer\Entity\Customer;
use App\Vendor\Entity\VendorPlan;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Subscription\Repository\SubscriptionRepository")
 * @ORM\Table(name="subscription")
 */

class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Customer\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private Customer $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Vendor\Entity\VendorPlan")
     * @ORM\JoinColumn(name="vendor_plan_id", referencedColumnName="id")
     */
    private VendorPlan $vendorPlan;

    /**
     * @ORM\Column(name="expires_at", type="datetimetz", nullable=true)
     */
    private ?\DateTimeInterface $expiresAt = null;

    public function getId(): int
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

    public function getVendorPlan(): VendorPlan
    {
        return $this->vendorPlan;
    }

    public function setVendorPlan(VendorPlan $vendorPlan): self
    {
        $this->vendorPlan = $vendorPlan;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}