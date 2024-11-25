<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Subscription\Entity\Subscription;
use Decimal\Decimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Customer\Repository\CustomerRepository")
 * @ORM\Table(name="customer")
 * @UniqueEntity(fields={"email"})
 */
class Customer implements PasswordAuthenticatedUserInterface, UserInterface, \Serializable, SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $country = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $locale = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $timezone = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\PositiveOrZero()
     * @Assert\Length(max=3)
     */
    private ?string $phoneIntlCode = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\PositiveOrZero()
     * @Assert\Length(max=3)
     */
    private ?string $phoneAreaCode = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\PositiveOrZero()
     * @Assert\Length(max=20)
     */
    private ?string $phoneNumber = null;

    /**
     * @ORM\Column(name="birth_date", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $birthDate = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $gender = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?int $height = null;

    /**
     * @ORM\Column(type="decimal", nullable=true, options={"precision": 11, "scale": 2})
     */
    private Decimal|string|null $lastWeight = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $goals = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $documents = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Subscription\Entity\Subscription", mappedBy="customer")
     */
    private Collection $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
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

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getGoals(): ?array
    {
        return $this->goals;
    }

    public function setGoals(?array $goals): self
    {
        $this->goals = $goals;

        return $this;
    }

    public function getDocument(string $documentType): ?string
    {
        return $this->documents[$documentType] ?? null;
    }

    public function getDocuments(): ?array
    {
        return $this->documents;
    }

    public function setDocuments(?array $documents): self
    {
        $this->documents = $documents;

        return $this;
    }

    public function getPhoneIntlCode(): ?string
    {
        return $this->phoneIntlCode;
    }

    public function setPhoneIntlCode(?string $phoneIntlCode): self
    {
        $this->phoneIntlCode = $phoneIntlCode;

        return $this;
    }

    public function getPhoneAreaCode(): ?string
    {
        return $this->phoneAreaCode;
    }

    public function setPhoneAreaCode(?string $phoneAreaCode): self
    {
        $this->phoneAreaCode = $phoneAreaCode;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getLastWeight(): ?Decimal
    {
        return null !== $this->lastWeight ? new Decimal($this->lastWeight) : null;
    }

    public function setLastWeight(Decimal|string $lastWeight): self
    {
        $this->lastWeight = is_string($lastWeight) ? new Decimal($lastWeight) : $lastWeight;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

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

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function getActiveSubscriptions(): Collection
    {
        $subscriptions = new ArrayCollection();

        /** @var Subscription $subscription */
        foreach ($this->subscriptions as $subscription) {
            if (! $subscription->isActive()) {
                continue;
            }

            $subscriptions->add($subscription);
        }

        return $subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        $this->subscriptions->add($subscription);

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
        return ! isset($this->id);
    }
}
