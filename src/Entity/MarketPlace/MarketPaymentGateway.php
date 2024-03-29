<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketPaymentGatewayRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketPaymentGatewayRepository::class)]
class MarketPaymentGateway
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $summary = null;

    #[ORM\Column]
    private bool $active;

    #[ORM\Column(length: 50)]
    private ?string $icon = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 100)]
    private ?string $handler_text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deleted_at = null;

    #[ORM\OneToMany(mappedBy: 'gateway', targetEntity: MarketPaymentGatewayMarket::class)]
    private Collection $marketPaymentGatewayMarkets;

    #[ORM\OneToMany(mappedBy: 'payment_gateway', targetEntity: MarketInvoice::class)]
    private Collection $marketInvoices;

    public function __construct()
    {
        $this->marketPaymentGatewayMarkets = new ArrayCollection();
        $this->marketInvoices = new ArrayCollection();
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getHandlerText(): string
    {
        return $this->handler_text;
    }

    public function setHandlerText(?string $handler_text): static
    {
        $this->handler_text = $handler_text;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     * @param DateTimeInterface|null $deleted_at
     * @return $this
     */
    public function setDeletedAt(?DateTimeInterface $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return Collection<int, MarketPaymentGatewayMarket>
     */
    public function getMarketPaymentGatewayMarkets(): Collection
    {
        return $this->marketPaymentGatewayMarkets;
    }

    /**
     * @param MarketPaymentGatewayMarket $marketPaymentGatewayMarket
     * @return $this
     */
    public function addMarketPaymentGatewayMarket(MarketPaymentGatewayMarket $marketPaymentGatewayMarket): static
    {
        if (!$this->marketPaymentGatewayMarkets->contains($marketPaymentGatewayMarket)) {
            $this->marketPaymentGatewayMarkets->add($marketPaymentGatewayMarket);
            $marketPaymentGatewayMarket->setGateway($this);
        }

        return $this;
    }

    /**
     * @param MarketPaymentGatewayMarket $marketPaymentGatewayMarket
     * @return $this
     */
    public function removeMarketPaymentGatewayMarket(MarketPaymentGatewayMarket $marketPaymentGatewayMarket): static
    {
        if ($this->marketPaymentGatewayMarkets->removeElement($marketPaymentGatewayMarket)) {
            // set the owning side to null (unless already changed)
            if ($marketPaymentGatewayMarket->getGateway() === $this) {
                $marketPaymentGatewayMarket->setGateway(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketInvoice>
     */
    public function getMarketInvoices(): Collection
    {
        return $this->marketInvoices;
    }

    public function addMarketInvoice(MarketInvoice $marketInvoice): static
    {
        if (!$this->marketInvoices->contains($marketInvoice)) {
            $this->marketInvoices->add($marketInvoice);
            $marketInvoice->setPaymentGateway($this);
        }

        return $this;
    }

    public function removeMarketInvoice(MarketInvoice $marketInvoice): static
    {
        if ($this->marketInvoices->removeElement($marketInvoice)) {
            // set the owning side to null (unless already changed)
            if ($marketInvoice->getPaymentGateway() === $this) {
                $marketInvoice->setPaymentGateway(null);
            }
        }

        return $this;
    }
}
