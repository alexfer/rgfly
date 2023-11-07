<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'category')]
#[UniqueEntity(fields: ['slug'], message: 'slug.unique')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: EntryCategory::class)]
    private Collection $entryCategories;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->entryCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param $slug
     * @return $this
     */
    public function setSlug($slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return Collection<int, EntryCategory>
     */
    public function getEntryCategories(): Collection
    {
        return $this->entryCategories;
    }

    public function addEntryCategory(EntryCategory $entryCategory): static
    {
        if (!$this->entryCategories->contains($entryCategory)) {
            $this->entryCategories->add($entryCategory);
            $entryCategory->setCategory($this);
        }

        return $this;
    }

    public function removeEntryCategory(EntryCategory $entryCategory): static
    {
        if ($this->entryCategories->removeElement($entryCategory)) {
            // set the owning side to null (unless already changed)
            if ($entryCategory->getCategory() === $this) {
                $entryCategory->setCategory();
            }
        }

        return $this;
    }
}
