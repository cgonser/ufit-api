<?php

namespace App\Program\Entity;

use App\Customer\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Program\Repository\ProgramAssignmentRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="program_assignment")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class ProgramAssignment
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
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $programId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Program\Entity\Program", inversedBy="assignments")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     */
    private Program $program;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $customerId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Customer\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private Customer $customer;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $isActive = true;

    /**
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $expiresAt = null;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getProgramId(): UuidInterface
    {
        return $this->programId;
    }

    public function setProgramId(UuidInterface $programId): self
    {
        $this->programId = $programId;

        return $this;
    }

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function setProgram(Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getCustomerId(): UuidInterface
    {
        return $this->customerId;
    }

    public function setCustomerId(UuidInterface $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
