<?php

namespace App\Entity\MarketPlace;

use App\Entity\MarketPlace\Enum\EnumOperation;
use App\Repository\MarketPlace\StoreOperationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreOperationRepository::class)]
class StoreOperation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(enumType: EnumOperation::class)]
    private ?EnumOperation $format = null;

    #[ORM\Column(length: 100)]
    private ?string $revision = null;

    #[ORM\ManyToOne(inversedBy: 'storeOperations')]
    private ?Store $store = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormat(): ?EnumOperation
    {
        return $this->format;
    }

    public function setFormat(EnumOperation $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getRevision(): ?string
    {
        return $this->revision;
    }

    public function setRevision(string $revision): static
    {
        $this->revision = $revision;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): static
    {
        $this->store = $store;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
