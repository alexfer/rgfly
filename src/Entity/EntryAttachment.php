<?php

namespace App\Entity;

use App\Repository\EntryAttachmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryAttachmentRepository::class)]
#[ORM\Table(name: 'entry_attachment')]
class EntryAttachment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(inversedBy: 'entryAttachments')]
    private ?Attach $attach = null;

    #[ORM\ManyToOne(inversedBy: 'entryAttachments')]
    private ?Entry $details = null;

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
}
