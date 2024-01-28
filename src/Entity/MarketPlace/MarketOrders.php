<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketOrdersRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketOrdersRepository::class)]
class MarketOrders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketOrders')]
    private ?Market $market = null;

    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $completed_at = null;

    #[ORM\OneToOne(mappedBy: 'orders', cascade: ['persist', 'remove'])]
    private ?MarketInvoice $marketInvoice = null;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: MarketOrdersProduct::class)]
    private Collection $marketOrdersProducts;

    #[ORM\OneToMany(mappedBy: 'marketOrders', targetEntity: MarketCustomer::class)]
    private Collection $customer;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->marketOrdersProducts = new ArrayCollection();
        $this->customer = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Market|null
     */
    public function getMarket(): ?Market
    {
        return $this->market;
    }

    /**
     * @param Market|null $market
     * @return $this
     */
    public function setMarket(?Market $market): static
    {
        $this->market = $market;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return $this
     */
    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return $this
     */
    public function setTotal(float $total): static
    {
        $this->total = $total;

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
    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completed_at;
    }

    /**
     * @param DateTimeInterface|null $completed_at
     * @return $this
     */
    public function setCompletedAt(?DateTimeInterface $completed_at): static
    {
        $this->completed_at = $completed_at;

        return $this;
    }

    /**
     * @return MarketInvoice|null
     */
    public function getMarketInvoice(): ?MarketInvoice
    {
        return $this->marketInvoice;
    }

    /**
     * @param MarketInvoice|null $marketInvoice
     * @return $this
     */
    public function setMarketInvoice(?MarketInvoice $marketInvoice): static
    {
        // unset the owning side of the relation if necessary
        if ($marketInvoice === null && $this->marketInvoice !== null) {
            $this->marketInvoice->setOrders(null);
        }

        // set the owning side of the relation if necessary
        if ($marketInvoice !== null && $marketInvoice->getOrders() !== $this) {
            $marketInvoice->setOrders($this);
        }

        $this->marketInvoice = $marketInvoice;

        return $this;
    }

    /**
     * @return Collection<int, MarketOrdersProduct>
     */
    public function getMarketOrdersProducts(): Collection
    {
        return $this->marketOrdersProducts;
    }

    /**
     * @param MarketOrdersProduct $marketOrdersProduct
     * @return $this
     */
    public function addMarketOrdersProduct(MarketOrdersProduct $marketOrdersProduct): static
    {
        if (!$this->marketOrdersProducts->contains($marketOrdersProduct)) {
            $this->marketOrdersProducts->add($marketOrdersProduct);
            $marketOrdersProduct->setOrders($this);
        }

        return $this;
    }

    /**
     * @param MarketOrdersProduct $marketOrdersProduct
     * @return $this
     */
    public function removeMarketOrdersProduct(MarketOrdersProduct $marketOrdersProduct): static
    {
        if ($this->marketOrdersProducts->removeElement($marketOrdersProduct)) {
            // set the owning side to null (unless already changed)
            if ($marketOrdersProduct->getOrders() === $this) {
                $marketOrdersProduct->setOrders(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketCustomer>
     */
    public function getCustomer(): Collection
    {
        return $this->customer;
    }

    public function addCustomer(MarketCustomer $customer): static
    {
        if (!$this->customer->contains($customer)) {
            $this->customer->add($customer);
            $customer->setMarketOrders($this);
        }

        return $this;
    }

    public function removeCustomer(MarketCustomer $customer): static
    {
        if ($this->customer->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getMarketOrders() === $this) {
                $customer->setMarketOrders(null);
            }
        }

        return $this;
    }
}
