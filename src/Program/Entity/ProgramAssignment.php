<?php

declare(strict_types=1);

namespace App\Program\Entity;

use DateTimeInterface;
use App\Customer\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use App\Program\Repository\ProgramAssignmentRepository;
use App\Program\Entity\Program;

#[ORM\Entity(repositoryClass: ProgramAssignmentRepository::class)]
#[ORM\Table(name: "program_assignment")]
class ProgramAssignment implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $programId;

    #[ORM\ManyToOne(targetEntity: Program::class, inversedBy: "assignments")]
    private Program $program;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $customerId;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\Column(type: "boolean", nullable: false, options: ["default" => true])]
    private bool $isActive = true;

    #[ORM\Column(name: "expires_at", type: "datetime", nullable: true)]
    private ?DateTimeInterface $expiresAt = null;

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

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
