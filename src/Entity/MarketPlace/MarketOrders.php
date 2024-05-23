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
    const STATUS = [
        'delivered' => 'delivered',
        'unreached' => 'unreached',
        'paid' => 'paid',
        'confirmed' => 'confirmed',
        'processing' => 'processing',
        'pending' => 'pending',
        'on-hold' => 'on-hold',
        'shipped' => 'shipped',
        'cancelled' => 'cancelled',
        'refused' => 'refused',
        'awaiting-return' => 'awaiting-return',
        'returned' => 'returned',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketOrders')]
    private ?Market $market = null;

    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '2', nullable: true)]
    private ?string $total = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $session = null;

    #[ORM\Column(length: 100)]
    private ?string $status;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $completed_at = null;

    #[ORM\OneToOne(mappedBy: 'orders', cascade: ['persist', 'remove'])]
    private ?MarketInvoice $marketInvoice = null;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: MarketOrdersProduct::class)]
    private Collection $marketOrdersProducts;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: MarketCustomerOrders::class)]
    private Collection $marketCustomerOrders;

    /**
     * @var Collection<int, MarketMessage>
     */
    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: MarketMessage::class)]
    private Collection $marketMessages;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->marketOrdersProducts = new ArrayCollection();
        $this->status = self::STATUS['processing'];
        $this->marketCustomerOrders = new ArrayCollection();
        $this->marketMessages = new ArrayCollection();
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
     * @return string|null
     */
    public function getTotal(): ?string
    {
        return $this->total;
    }

    /**
     * @param string $total
     * @return $this
     */
    public function setTotal(string $total): static
    {
        //dd(number_format($total, 10, '.', ''));
        $this->total = number_format($total, 2, '.', '');

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

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return $this
     */
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

    /**
     * @param MarketCustomerOrders $marketCustomerOrder
     * @return $this
     */
    public function addMarketCustomerOrder(MarketCustomerOrders $marketCustomerOrder): static
    {
        if (!$this->marketCustomerOrders->contains($marketCustomerOrder)) {
            $this->marketCustomerOrders->add($marketCustomerOrder);
            $marketCustomerOrder->setOrders($this);
        }

        return $this;
    }

    /**
     * @param MarketCustomerOrders $marketCustomerOrder
     * @return $this
     */
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

    /**
     * @return Collection<int, MarketMessage>
     */
    public function getMarketMessages(): Collection
    {
        return $this->marketMessages;
    }

    /**
     * @param MarketMessage $marketMessage
     * @return $this
     */
    public function addMarketMessage(MarketMessage $marketMessage): static
    {
        if (!$this->marketMessages->contains($marketMessage)) {
            $this->marketMessages->add($marketMessage);
            $marketMessage->setOrders($this);
        }

        return $this;
    }

    /**
     * @param MarketMessage $marketMessage
     * @return $this
     */
    public function removeMarketMessage(MarketMessage $marketMessage): static
    {
        if ($this->marketMessages->removeElement($marketMessage)) {
            // set the owning side to null (unless already changed)
            if ($marketMessage->getOrders() === $this) {
                $marketMessage->setOrders(null);
            }
        }

        return $this;
    }
}
