<?php declare(strict_types=1);

namespace App\Entity\MarketPlace;

use App\Entity\Attach;
use App\Repository\MarketPlace\StoreCarrierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreCarrierRepository::class)]
class StoreCarrier
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private string $slug;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Attach $attach = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $shipping_amount = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_enabled = null;

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
    public function getSlug(): ?string
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
     * @return Attach|null
     */
    public function getAttach(): ?Attach
    {
        return $this->attach;
    }

    /**
     * @param Attach|null $attach
     * @return $this
     */
    public function setAttach(?Attach $attach): static
    {
        $this->attach = $attach;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinkUrl(): ?string
    {
        return $this->link_url;
    }

    /**
     * @param string|null $link_url
     * @return $this
     */
    public function setLinkUrl(?string $link_url): static
    {
        $this->link_url = $link_url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShippingAmount(): ?string
    {
        return $this->shipping_amount;
    }

    /**
     * @param string|null $shipping_amount
     * @return $this
     */
    public function setShippingAmount(?string $shipping_amount): static
    {
        $this->shipping_amount = $shipping_amount;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isEnabled(): ?bool
    {
        return $this->is_enabled;
    }

    /**
     * @param bool|null $is_enabled
     * @return $this
     */
    public function setEnabled(?bool $is_enabled): static
    {
        $this->is_enabled = $is_enabled;

        return $this;
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
}
