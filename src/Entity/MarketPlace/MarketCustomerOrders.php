<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketCustomerOrdersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketCustomerOrdersRepository::class)]
class MarketCustomerOrders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketCustomerOrders')]
    private ?MarketCustomer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'marketCustomerOrders')]
    private ?MarketOrders $orders = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?MarketCustomer
    {
        return $this->customer;
    }

    public function setCustomer(?MarketCustomer $customer): static
    {
        $this->cusomer = $customer;

        return $this;
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
}
