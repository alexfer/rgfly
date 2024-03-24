<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketPaymentGatewayMarketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketPaymentGatewayMarketRepository::class)]
class MarketPaymentGatewayMarket
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketPaymentGatewayMarkets')]
    private ?MarketPaymentGateway $gateway = null;

    #[ORM\ManyToOne(inversedBy: 'marketPaymentGatewayMarkets')]
    private ?Market $market = null;

    #[ORM\Column(nullable: true)]
    private ?bool $active = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return MarketPaymentGateway|null
     */
    public function getGateway(): ?MarketPaymentGateway
    {
        return $this->gateway;
    }

    /**
     * @param MarketPaymentGateway|null $gateway
     * @return $this
     */
    public function setGateway(?MarketPaymentGateway $gateway): static
    {
        $this->gateway = $gateway;

        return $this;
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
}
