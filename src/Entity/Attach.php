<?php

namespace App\Entity;

use App\Repository\AttachRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttachRepository::class)]
class Attach
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $mime;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $size = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected DateTime $created_at;

    #[ORM\OneToMany(mappedBy: 'attach', targetEntity: EntryAttachment::class)]
    private Collection $entryAttachments;

    #[ORM\ManyToOne(inversedBy: 'attach')]
    private ?UserDetails $userDetails = null;

    #[ORM\OneToOne(mappedBy: 'attach', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->size = 0;
        $this->entryAttachments = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMime(): ?string
    {
        return $this->mime;
    }

    /**
     * @param string $mime
     * @return $this
     */
    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Collection<int, EntryAttachment>
     */
    public function getEntryAttachments(): Collection
    {
        return $this->entryAttachments;
    }

    public function addEntryAttachment(EntryAttachment $entryAttachment): static
    {
        if (!$this->entryAttachments->contains($entryAttachment)) {
            $this->entryAttachments->add($entryAttachment);
            $entryAttachment->setAttach($this);
        }

        return $this;
    }

    public function removeEntryAttachment(EntryAttachment $entryAttachment): static
    {
        if ($this->entryAttachments->removeElement($entryAttachment)) {
            // set the owning side to null (unless already changed)
            if ($entryAttachment->getAttach() === $this) {
                $entryAttachment->setAttach(null);
            }
        }

        return $this;
    }

    public function getUserDetails(): ?UserDetails
    {
        return $this->userDetails;
    }

    public function setUserDetails(?UserDetails $userDetails): static
    {
        $this->userDetails = $userDetails;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setAttach(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getAttach() !== $this) {
            $user->setAttach($this);
        }

        $this->user = $user;

        return $this;
    }
}
