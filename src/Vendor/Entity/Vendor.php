<?php

namespace App\Vendor\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Vendor\Repository\VendorRepository")
 * @ORM\Table(name="vendor")
 * @UniqueEntity(fields={"email"})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class Vendor implements UserInterface, \Serializable
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
     * @ORM\OneToMany(targetEntity="VendorPlan", mappedBy="vendor", cascade={"persist"})
     */
    private Collection $plans;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $displayName = null;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private string $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $photo = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $biography = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="json", nullable=false, options={"default": "{}"})
     */
    private array $socialLinks = [];

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $country = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $locale = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $timezone = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $allowEmailMarketing = true;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->email = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSocialLinks(): array
    {
        return $this->socialLinks;
    }

    public function getSocialLink(string $network): ?string
    {
        return $this->socialLinks[$network] ?? null;
    }

    public function setSocialLink(string $network, ?string $link): self
    {
        if (null !== $link) {
            $this->socialLinks[$network] = $link;
        } elseif (array_key_exists($network, $this->socialLinks)) {
            unset($this->socialLinks[$network]);
        }

        return $this;
    }

    public function setSocialLinks(array $socialLinks): self
    {
        $this->socialLinks = $socialLinks;

        return $this;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(VendorPlan $vendorPlan): self
    {
        $vendorPlan->setVendor($this);

        $this->plans[] = $vendorPlan;

        return $this;
    }

    public function allowEmailMarketing(): bool
    {
        return $this->allowEmailMarketing;
    }

    public function setAllowEmailMarketing(bool $allowEmailMarketing): self
    {
        $this->allowEmailMarketing = $allowEmailMarketing;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // We're using bcrypt in security.yaml to encode the password, so
        // the salt value is built-in and and you don't have to generate one
        // See https://en.wikipedia.org/wiki/Bcrypt

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->email, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->email, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
