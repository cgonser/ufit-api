<?php

declare(strict_types=1);

namespace App\Vendor\Entity;

use App\Vendor\Repository\VendorRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: VendorRepository::class)]
#[Table(name: 'vendor')]
#[UniqueEntity(fields: ['email'])]
class Vendor implements PasswordAuthenticatedUserInterface, UserInterface, Serializable, SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    /**
     * @var VendorPlan[]|Collection<int, VendorPlan>
     */
    #[OneToMany(mappedBy: 'vendor', targetEntity: 'VendorPlan', cascade: ['persist'])]
    private Collection $plans;

    #[Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $displayName = null;

    #[Column(type: 'string', unique: true)]
    #[NotBlank]
    #[Email]
    private string $email;

    #[Column(type: 'string', nullable: true)]
    private ?string $photo = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $biography = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $password = null;

    #[Column(type: 'json')]
    private array $roles = [];

    #[Column(type: 'json', options: [
        'default' => '{}',
    ])]
    private array $socialLinks = [];

    #[Column(type: 'string', unique: true, nullable: true)]
    private ?string $slug = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $country = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $locale = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $timezone = null;

    #[Column(type: 'boolean', options: [
        'default' => true,
    ])]
    private bool $allowEmailMarketing = true;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $welcomeEmailSentAt = null;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
    }

    public function getId(): UuidInterface
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

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->email = $username;

        return $this;
    }

    public function getEmail(): string
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

    /**
     * @return mixed[]
     */
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

    /**
     * @param mixed[] $socialLinks
     */
    public function setSocialLinks(array $socialLinks): self
    {
        $this->socialLinks = $socialLinks;

        return $this;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     *
     * @return mixed[]
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

    /**
     * @param mixed[] $roles
     */
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

    public function getWelcomeEmailSentAt(): ?DateTimeInterface
    {
        return $this->welcomeEmailSentAt;
    }

    public function setWelcomeEmailSentAt(?\DateTimeInterface $welcomeEmailSentAt): self
    {
        $this->welcomeEmailSentAt = $welcomeEmailSentAt;

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
        [$this->id, $this->email, $this->password] = unserialize($serialized, [
            'allowed_classes' => false,
        ]);
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
