<?php

namespace App\Entity;

use App\Repository\EntryAttachmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryAttachmentRepository::class)]
class EntryAttachment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'entryAttachments')]
    private ?Attach $attach = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'entryAttachments')]
    private ?Entry $details = null;

    #[ORM\Column(type: Types::INTEGER, length: 1, nullable: true)]
    private ?int $in_use = 0;

    public function __construct()
    {
        $this->in_use = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttach(): ?Attach
    {
        return $this->attach;
    }

    public function setAttach(?Attach $attach): static
    {
        $this->attach = $attach;

        return $this;
    }

    public function getDetails(): ?Entry
    {
        return $this->details;
    }

    public function setDetails(?Entry $details): static
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInUse(): ?int
    {
        return $this->in_use;
    }

    /**
     * @param int $in_use
     * @return $this
     */
    public function setInUse(int $in_use): self
    {
        $this->in_use = $in_use;

        return $this;
    }
}