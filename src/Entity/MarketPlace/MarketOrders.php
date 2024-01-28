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
    const array STATUS = [
        'delivered' => 'Delivered',
        'unreached' => 'Unreached',
        'paid' => 'Paid',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'pending' => 'Pending',
        'on-hold' => 'On Hold',
        'shipped' => 'Shipped',
        'cancelled' => 'Cancelled',
        'refused' => 'Refused',
        'awaiting-return' => 'Awaiting Return',
        'returned' => 'Returned',
    ];

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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $session = null;

    #[ORM\Column(length: 100)]
    private ?string $status;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: MarketCustomerOrders::class)]
    private Collection $marketCustomerOrders;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->marketOrdersProducts = new ArrayCollection();
        $this->status = array_flip(self::STATUS)['Processing'];
        $this->marketCustomerOrders = new ArrayCollection();
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
     * @return string|null
     */
    public function getSession(): ?string
    {
        return $this->session;
    }

    /**
     * @param string|null $session
     * @return $this
     */
    public function setSession(?string $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

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
            $marketCustomerOrder->setOrders($this);
        }

        return $this;
    }

    public function removeMarketCustomerOrder(MarketCustomerOrders $marketCustomerOrder): static
    {
        if ($this->marketCustomerOrders->removeElement($marketCustomerOrder)) {
            // set the owning side to null (unless already changed)
            if ($marketCustomerOrder->getOrders() === $this) {
                $marketCustomerOrder->setOrders(null);
            }
        }

        return $this;
    }
}
