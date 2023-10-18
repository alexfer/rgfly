<?php

namespace App\Entity;

use App\Entity\{
    User,
    Attach,
};
use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection,
};
use App\Repository\UserDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDetailsRepository::class)]
#[ORM\Table(name: 'user_details')]
class UserDetails
{

    const CONSTRAINTS = [
        'first_name' => [
            'min' => 3,
            'max' => 200,
        ],
        'last_name' => [
            'min' => 2,
            'max' => 200,
        ],
        'about' => [
            'min' => 100,
            'max' => 65535,
        ],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, name: "user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: Types::TEXT, length: 65535, nullable: true)]
    private ?string $about = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\OneToMany(targetEntity: Attach::class, mappedBy: 'details', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['created_at' => 'DESC'])]
    #[ORM\setMaxResults(1)]
    private ?Collection $attach;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $attach_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $date_birth = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updated_at = null;

    public function __construct()
    {
        $this->updated_at = new \DateTime();
        $this->date_birth = new \DateTime('2005-01-01');
        $this->attach_id = 0;
        $this->attach = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getAttach(): Collection
    {
        return $this->attach;
    }

    public function setAttach($attach): static
    {
        $this->attach = $attach;

        return $this;
    }

    public function getAttachId(): ?int
    {
        return $this->attach_id;
    }

    public function setAttachId(int $attach_id): static
    {
        $this->attach_id = $attach_id;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): static
    {
        $this->about = $about;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->phone;
    }

    public function setUpdatedAt(\DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateBirth(): ?\DateTime
    {
        return $this->date_birth;
    }

    public function setDateBirth(\DateTime $date_birth): static
    {
        $this->date_birth = $date_birth;

        return $this;
    }
}
