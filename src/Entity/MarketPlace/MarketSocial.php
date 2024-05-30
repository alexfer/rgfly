<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketSocialRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketSocialRepository::class)]
class MarketSocial
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketSocials')]
    private ?Market $market = null;

    #[ORM\Column(length: 4096)]
    private ?string $source = null;

    #[ORM\Column]
    private ?string $source_name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_active = null;

    public const array NAME = [
        'Facebook' => 'facebook',
        'Instagram' => 'instagram',
        'Twitter' => 'twitter',
        'Youtube' => 'youtube',
    ];

    public function __construct()
    {
        $this->is_active = false;
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
    public function getSourceName(): ?string
    {
        return $this->source_name;
    }

    /**
     * @param string $source_name
     * @return $this
     */
    public function setSourceName(string $source_name): static
    {
        $this->source_name = $source_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return $this
     */
    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    /**
     * @param bool|null $is_active
     * @return $this
     */
    public function setActive(?bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }
}
