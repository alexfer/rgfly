<?php

namespace App\Entity;

use App\Repository\AttachRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttachRepository::class)]
#[ORM\Table(name: 'file')]
class Attach
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserDetails::class, inversedBy: 'file')]
    #[ORM\JoinColumn(nullable: false, name: "relation_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?UserDetails $details = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $relation_id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $mime;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $size = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private \DateTime $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->size = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getRelationId(): ?int
    {
        return $this->relation_id;
    }

    public function setRelationId(int $relation_id): self
    {
        $this->relation_id = $relation_id;

        return $this;
    }

    public function getDetails(): ?UserDetails
    {
        return $this->details;
    }

    public function setDetails(?UserDetails $details): self
    {
        $this->details = $details;

        return $this;
    }
}
