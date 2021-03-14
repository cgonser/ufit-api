<?php

namespace App\Program\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass="App\Program\Repository\ProgramAssetRepository")
 * @ORM\Table(name="program_asset")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class ProgramAsset
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
     * @ORM\ManyToOne(targetEntity="App\Program\Entity\Program", inversedBy="assets")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     */
    private Program $program;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $filename = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $type = null;

    public function getId(): UuidInterface
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
