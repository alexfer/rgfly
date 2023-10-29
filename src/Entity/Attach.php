<?php

namespace App\Entity;

use App\Entity\UserDetails;
use App\Repository\AttachRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttachRepository::class)]
#[ORM\Table(name: 'attach')]
class Attach
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserDetails::class, inversedBy: 'attach')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?UserDetails $user = null;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $mime;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $size = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private \DateTime $created_at;

    #[ORM\OneToMany(mappedBy: 'attach', targetEntity: EntryAttachment::class)]
    private Collection $entryAttachments;

    public function __construct()
    {
        $this->created_at = new \DateTime();
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
     * @return UserDetails
     */
    public function getUser(): UserDetails
    {
        return $this->user;
    }

    /**
     * @param UserDetails $user
     * @return $this
     */
    public function setUser(UserDetails $user): self
    {
        $this->user = $user;

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
}
