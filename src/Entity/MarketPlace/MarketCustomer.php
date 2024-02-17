<?php

namespace App\Entity\MarketPlace;

use App\Entity\User;
use App\Repository\MarketPlace\MarketCustomerRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketCustomerRepository::class)]
class MarketCustomer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 100)]
    private ?string $phone = null;

    #[ORM\Column(length: 5)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $member = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: MarketCustomerOrders::class)]
    private Collection $marketCustomerOrders;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: MarketCustomerMessage::class)]
    private Collection $marketCustomerMessages;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->marketCustomerOrders = new ArrayCollection();
        $this->marketCustomerMessages = new ArrayCollection();
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, MarketCustomerOrders>
     */
    public function getMarketCustomerOrders(): Collection
    {
        return $this->marketCustomerOrders;
    }

    public function addMarketCustomerOrder(MarketCustomerOrders $marketCustomerOrder): static
    {
        if (!$this->marketCustomerOrders->contains($marketCustomerOrder)) {
            $this->marketCustomerOrders->add($marketCustomerOrder);
            $marketCustomerOrder->setCustomer($this);
        }

        return $this;
    }

    public function removeMarketCustomerOrders(MarketCustomerOrders $marketCustomerOrder): static
    {
        if ($this->marketCustomerOrders->removeElement($marketCustomerOrder)) {
            // set the owning side to null (unless already changed)
            if ($marketCustomerOrder->getCustomer() === $this) {
                $marketCustomerOrder->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketCustomerMessage>
     */
    public function getMarketCustomerMessages(): Collection
    {
        return $this->marketCustomerMessages;
    }

    /**
     * @param MarketCustomerMessage $marketCustomerMessage
     * @return $this
     */
    public function addMarketCustomerMessage(MarketCustomerMessage $marketCustomerMessage): static
    {
        if (!$this->marketCustomerMessages->contains($marketCustomerMessage)) {
            $this->marketCustomerMessages->add($marketCustomerMessage);
            $marketCustomerMessage->setCustomer($this);
        }

        return $this;
    }

    /**
     * @param MarketCustomerMessage $marketCustomerMessage
     * @return $this
     */
    public function removeMarketCustomerMessage(MarketCustomerMessage $marketCustomerMessage): static
    {
        if ($this->marketCustomerMessages->removeElement($marketCustomerMessage)) {
            // set the owning side to null (unless already changed)
            if ($marketCustomerMessage->getCustomer() === $this) {
                $marketCustomerMessage->setCustomer(null);
            }
        }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
