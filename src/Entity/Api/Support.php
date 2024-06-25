<?php

declare(strict_types=1);

namespace App\Entity\Api;

use ApiPlatform\Metadata\{ApiResource, Delete, Get, GetCollection, Post, Put};
use App\Entity\User;
use App\Repository\SupportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/support/{id}',
            requirements: ['id' => '\d+'],
            security: "is_granted('ROLE_USER'",
            name: 'ROLE_USER',
        ),
        new Post(
            uriTemplate: '/support',
            security: 'is_granted("ROLE_ADMIN")',
        ),
        new Put(
            uriTemplate: '/support/{id}', requirements: ['id' => '\d+'], security: "is_granted('ROLE_ADMIN')"),
        new Delete(uriTemplate: '/support/{id}', requirements: ['id' => '\d+'], security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            uriTemplate: '/supports',
            normalizationContext: ['groups' => ['read']],
            security: "is_granted('ROLE_ADMIN')",
        ),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    openapiContext: [
        'summary' => 'Retrieve a collection of questions',
        'description' => 'This endpoint returns a collection of questions.',
    ],
    paginationItemsPerPage: 10
)]
#[ORM\Entity(repositoryClass: SupportRepository::class)]
#[ORM\Table(name: 'support', indexes: [new ORM\Index(columns: ['name'], name: 'name_idx')])]
class Support
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read', 'write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['read', 'write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['read'])]
    private ?bool $active;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->active = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeImmutable|null $updated_at
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function getUser(User $user): bool
    {
        return in_array(User::ROLE_ADMIN, $user->getRoles(), true);
    }

    /**
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
