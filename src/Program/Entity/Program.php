<?php

declare(strict_types=1);

namespace App\Program\Entity;

use App\Vendor\Entity\Vendor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
use App\Program\Repository\ProgramRepository;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[ORM\Table(name: "program")]
class Program implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $vendorId;

    #[ORM\ManyToOne(targetEntity: Vendor::class)]
    private Vendor $vendor;

    #[ORM\Column(type: "text", nullable: false)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $level = null;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $goals = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "boolean", nullable: false, options: ["default" => false])]
    private bool $isTemplate = false;

    #[ORM\Column(type: "boolean", nullable: false, options: ["default" => true])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: "program", targetEntity: "ProgramAsset", cascade: ["persist"])]
    private Collection $assets;

    #[ORM\OneToMany(mappedBy: "program", targetEntity: "ProgramAssignment", cascade: ["persist"])]
    private Collection $assignments;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->assignments = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getVendorId(): UuidInterface
    {
        return $this->vendorId;
    }

    public function setVendorId(UuidInterface $vendorId): self
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    public function getVendor(): Vendor
    {
        return $this->vendor;
    }

    public function setVendor(Vendor $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
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

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getGoals(): ?array
    {
        return $this->goals;
    }

    public function setGoals(?array $goals): self
    {
        $this->goals = $goals;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(ProgramAsset $programAsset): self
    {
        $programAsset->setProgram($this);

        $this->assets[] = $programAsset;

        return $this;
    }

    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(ProgramAssignment $programAssignment): self
    {
        $programAssignment->setProgram($this);

        $this->assignments[] = $programAssignment;

        return $this;
    }

    public function isTemplate(): bool
    {
        return $this->isTemplate;
    }

    public function setIsTemplate(bool $isTemplate): self
    {
        $this->isTemplate = $isTemplate;

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
}
