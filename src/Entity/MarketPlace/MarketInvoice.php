<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketInvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketInvoiceRepository::class)]
class MarketInvoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'marketInvoice', cascade: ['persist', 'remove'])]
    private ?MarketOrders $orders = null;

    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\Column]
    private ?float $tax = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $paid_at = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?MarketPaymentMethod $payment_method = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrders(): ?MarketOrders
    {
        return $this->orders;
    }

    public function setOrders(?MarketOrders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(float $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPayedAt(): ?\DateTimeInterface
    {
        return $this->paid_at;
    }

    public function setPayedAt(?\DateTimeInterface $paid_at): static
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    public function getPaymentMethod(): ?MarketPaymentMethod
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(?MarketPaymentMethod $payment_method): static
    {
        $this->payment_method = $payment_method;

        return $this;
    }
}
