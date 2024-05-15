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
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
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

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: MarketCustomerOrders::class)]
    private Collection $marketCustomerOrders;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: MarketCustomerMessage::class)]
    private Collection $marketCustomerMessages;

    #[ORM\OneToOne(mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private ?MarketAddress $marketAddress = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: MarketWishlist::class)]
    private Collection $marketWishlists;

    /**
     * @var Collection<int, MarketCouponUsage>
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: MarketCouponUsage::class)]
    private Collection $marketCouponUsages;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updated_at = null;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->marketCustomerOrders = new ArrayCollection();
        $this->marketCustomerMessages = new ArrayCollection();
        $this->marketWishlists = new ArrayCollection();
        $this->marketCouponUsages = new ArrayCollection();
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
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     * @return $this
     */
    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     * @return $this
     */
    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getMember(): ?User
    {
        return $this->member;
    }

    /**
     * @param User|null $member
     * @return $this
     */
    public function setMember(?User $member): static
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param DateTimeInterface|null $updated_at
     * @return $this
     */
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

    /**
     * @param MarketCustomerOrders $marketCustomerOrder
     * @return $this
     */
    public function addMarketCustomerOrder(MarketCustomerOrders $marketCustomerOrder): static
    {
        if (!$this->marketCustomerOrders->contains($marketCustomerOrder)) {
            $this->marketCustomerOrders->add($marketCustomerOrder);
            $marketCustomerOrder->setCustomer($this);
        }

        return $this;
    }

    /**
     * @param MarketCustomerOrders $marketCustomerOrder
     * @return $this
     */
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

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return MarketAddress|null
     */
    public function getMarketAddress(): ?MarketAddress
    {
        return $this->marketAddress;
    }

    /**
     * @param MarketAddress|null $marketAddress
     * @return $this
     */
    public function setMarketAddress(?MarketAddress $marketAddress): static
    {
        // unset the owning side of the relation if necessary
        if ($marketAddress === null && $this->marketAddress !== null) {
            $this->marketAddress->setCustomer(null);
        }

        // set the owning side of the relation if necessary
        if ($marketAddress !== null && $marketAddress->getCustomer() !== $this) {
            $marketAddress->setCustomer($this);
        }

        $this->marketAddress = $marketAddress;

        return $this;
    }

    /**
     * @return Collection<int, MarketWishlist>
     */
    public function getMarketWishlists(): Collection
    {
        return $this->marketWishlists;
    }

    /**
     * @param MarketWishlist $marketWishlist
     * @return $this
     */
    public function addMarketWishlist(MarketWishlist $marketWishlist): static
    {
        if (!$this->marketWishlists->contains($marketWishlist)) {
            $this->marketWishlists->add($marketWishlist);
            $marketWishlist->setCustomer($this);
        }

        return $this;
    }

    /**
     * @param MarketWishlist $marketWishlist
     * @return $this
     */
    public function removeMarketWishlist(MarketWishlist $marketWishlist): static
    {
        if ($this->marketWishlists->removeElement($marketWishlist)) {
            // set the owning side to null (unless already changed)
            if ($marketWishlist->getCustomer() === $this) {
                $marketWishlist->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketCouponUsage>
     */
    public function getMarketCouponUsages(): Collection
    {
        return $this->marketCouponUsages;
    }

    /**
     * @param MarketCouponUsage $marketCouponUsage
     * @return $this
     */
    public function addMarketCouponUsage(MarketCouponUsage $marketCouponUsage): static
    {
        if (!$this->marketCouponUsages->contains($marketCouponUsage)) {
            $this->marketCouponUsages->add($marketCouponUsage);
            $marketCouponUsage->setCustomer($this);
        }

        return $this;
    }

    /**
     * @param MarketCouponUsage $marketCouponUsage
     * @return $this
     */
    public function removeMarketCouponUsage(MarketCouponUsage $marketCouponUsage): static
    {
        if ($this->marketCouponUsages->removeElement($marketCouponUsage)) {
            // set the owning side to null (unless already changed)
            if ($marketCouponUsage->getCustomer() === $this) {
                $marketCouponUsage->setCustomer(null);
            }
        }

        return $this;
    }
}
