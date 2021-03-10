<?php

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\CustomerPhotoRepository")
 * @ORM\Table(name="customer_photo")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class CustomerPhoto
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
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    private Customer $customer;

    /**
     * @ORM\ManyToOne(targetEntity="PhotoType")
     * @ORM\JoinColumn(name="photo_type_id", referencedColumnName="id")
     */
    private PhotoType $photoType;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $filename;

    /**
     * @ORM\Column(name="taken_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $takenAt = null;

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

    public function getPhotoType(): PhotoType
    {
        return $this->photoType;
    }

    public function setPhotoType(PhotoType $photoType): self
    {
        $this->photoType = $photoType;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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