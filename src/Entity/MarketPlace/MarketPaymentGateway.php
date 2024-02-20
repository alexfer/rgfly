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
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $summary = null;

    #[ORM\Column(nullable: true)]
    private ?bool $active = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $translation_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deleted_at = null;

    #[ORM\OneToMany(mappedBy: 'gateway', targetEntity: MarketPaymentGatewayMarket::class)]
    private Collection $marketPaymentGatewayMarkets;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $handler_translation_id = null;

    public function __construct()
    {
        $this->marketPaymentGatewayMarkets = new ArrayCollection();
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
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     * @return $this
     */
    public function setActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return $this
     */
    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTranslationId(): ?string
    {
        return $this->translation_id;
    }

    /**
     * @param string|null $translation_id
     * @return $this
     */
    public function setTranslationId(?string $translation_id): static
    {
        $this->translation_id = $translation_id;

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

    public function getHandlerTranslationId(): ?string
    {
        return $this->handler_translation_id;
    }

    public function setHandlerTranslationId(?string $handler_translation_id): static
    {
        $this->handler_translation_id = $handler_translation_id;

        return $this;
    }
}
