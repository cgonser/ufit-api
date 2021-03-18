<?php

namespace App\Program\Entity;

use App\Vendor\Entity\Vendor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Program\Repository\ProgramRepository")
 * @ORM\Table(name="program")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class Program
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
    private UuidInterface $vendorId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Vendor\Entity\Vendor")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     */
    private Vendor $vendor;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $level;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $goals = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $isTemplate = false;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $isActive = true;

    /**
     * @ORM\OneToMany(targetEntity="ProgramAsset", mappedBy="program", cascade={"persist"})
     */
    private Collection $assets;

    /**
     * @ORM\OneToMany(targetEntity="ProgramAssignment", mappedBy="program", cascade={"persist"})
     */
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

    public function setVendorId(UuidInterface $vendorId): Program
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
