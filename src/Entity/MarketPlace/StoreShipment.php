<?php declare(strict_types=1);

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\StoreShipmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreShipmentRepository::class)]
class StoreShipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tracking_number = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $tracking_number_url = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?StoreOrders $orders = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?StoreCarrier $carrier = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?StoreCoupon $coupon = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $shipped_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $received_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $returned_at = null;

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
    public function getTrackingNumber(): ?string
    {
        return $this->tracking_number;
    }

    /**
     * @param string|null $tracking_number
     * @return $this
     */
    public function setTrackingNumber(?string $tracking_number): static
    {
        $this->tracking_number = $tracking_number;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrackingNumberUrl(): ?string
    {
        return $this->tracking_number_url;
    }

    /**
     * @param string|null $tracking_number_url
     * @return $this
     */
    public function setTrackingNumberUrl(?string $tracking_number_url): static
    {
        $this->tracking_number_url = $tracking_number_url;

        return $this;
    }

    /**
     * @return StoreOrders|null
     */
    public function getOrders(): ?StoreOrders
    {
        return $this->orders;
    }

    /**
     * @param StoreOrders|null $orders
     * @return $this
     */
    public function setOrders(?StoreOrders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @return StoreCarrier|null
     */
    public function getCarrier(): ?StoreCarrier
    {
        return $this->carrier;
    }

    /**
     * @param StoreCarrier|null $carrier
     * @return $this
     */
    public function setCarrier(?StoreCarrier $carrier): static
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getShippedAt(): ?\DateTimeInterface
    {
        return $this->shipped_at;
    }

    /**
     * @param \DateTimeInterface|null $shipped_at
     * @return $this
     */
    public function setShippedAt(?\DateTimeInterface $shipped_at): static
    {
        $this->shipped_at = $shipped_at;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getReceivedAt(): ?\DateTimeInterface
    {
        return $this->received_at;
    }

    /**
     * @param \DateTimeInterface|null $received_at
     * @return $this
     */
    public function setReceivedAt(?\DateTimeInterface $received_at): static
    {
        $this->received_at = $received_at;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getReturnedAt(): ?\DateTimeInterface
    {
        return $this->returned_at;
    }

    /**
     * @param \DateTimeInterface|null $returned_at
     * @return $this
     */
    public function setReturnedAt(?\DateTimeInterface $returned_at): static
    {
        $this->returned_at = $returned_at;

        return $this;
    }

    /**
     * @return StoreCoupon|null
     */
    public function getCoupon(): ?StoreCoupon
    {
        return $this->coupon;
    }

    /**
     * @param StoreCoupon|null $coupon
     * @return $this
     */
    public function setCoupon(?StoreCoupon $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }
}
