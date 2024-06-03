<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\StoreInvoiceRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreInvoiceRepository::class)]
class StoreInvoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '2')]
    private ?string $tax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '2', nullable: true)]
    private ?string $amount = null;

    #[ORM\OneToOne(inversedBy: 'storeInvoice', cascade: ['persist', 'remove'])]
    private ?StoreOrders $orders = null;

    #[ORM\ManyToOne(inversedBy: 'storeInvoices')]
    private ?StorePaymentGateway $payment_gateway = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $paid_at = null;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
    public function getTax(): ?string
    {
        return $this->tax;
    }

    /**
     * @param string $tax
     * @return $this
     */
    public function setTax(string $tax): static
    {
        $this->tax = number_format($tax, 2, '.', '');

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount(string $amount): static
    {
        $this->amount = number_format($amount, 2, '.', '');;

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
    public function getPayedAt(): ?DateTimeInterface
    {
        return $this->paid_at;
    }

    /**
     * @param DateTimeInterface|null $paid_at
     * @return $this
     */
    public function setPayedAt(?DateTimeInterface $paid_at): static
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    /**
     * @return StorePaymentGateway|null
     */
    public function getPaymentGateway(): ?StorePaymentGateway
    {
        return $this->payment_gateway;
    }

    /**
     * @param StorePaymentGateway|null $payment_gateway
     * @return $this
     */
    public function setPaymentGateway(?StorePaymentGateway $payment_gateway): static
    {
        $this->payment_gateway = $payment_gateway;

        return $this;
    }

}
