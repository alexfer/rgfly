<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[UniqueEntity(fields: ['email'], message: 'email.unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [self::ROLE_USER];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $deleted_at = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserDetails $userDetails = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Attach $attach = null;

    final public const ROLE_USER = 'ROLE_USER';
    final public const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct()
    {
        $this->created_at = new DateTime();
    }

    /**
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * 
     * @param string $email
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * 
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @see UserInterface
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }    

    /**
     * @see UserInterface
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * 
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * 
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * 
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * 
     * @param DateTime $created_at
     * @return self
     */
    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * 
     * @return DateTime|null
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deleted_at;
    }

    /**
     * 
     * @param DateTime|null $deleted_at
     * @return self
     */
    public function setDeletedAt(?DateTime $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * 
     * @return Attach|null
     */
    public function getAttach(): ?Attach
    {
        return $this->attach;
    }

    /**
     * 
     * @param Attach|null $attach
     * @return static
     */
    public function setAttach(?Attach $attach): static
    {
        $this->attach = $attach;

        return $this;
    }

    /**
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * 
     * @return UserDetails|null
     */
    public function getUserDetails(): ?UserDetails
    {
        return $this->userDetails;
    }

    /**
     * 
     * @param UserDetails|null $userDetails
     * @return static
     */
    public function setUserDetails(?UserDetails $userDetails): static
    {
        // unset the owning side of the relation if necessary
        if ($userDetails === null && $this->userDetails !== null) {
            $this->userDetails->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($userDetails !== null && $userDetails->getUser() !== $this) {
            $userDetails->setUser($this);
        }

        $this->userDetails = $userDetails;

        return $this;
    }
}
