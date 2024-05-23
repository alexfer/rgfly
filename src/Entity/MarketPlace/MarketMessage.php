<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketMessageRepository::class)]
class MarketMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'marketMessages')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $marketMessages;

    #[ORM\ManyToOne(inversedBy: 'marketMessages')]
    private ?Market $market = null;

    #[ORM\ManyToOne(inversedBy: 'marketMessages')]
    private ?MarketCustomer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'marketMessages')]
    private ?MarketProduct $product = null;
    #[ORM\ManyToOne(inversedBy: 'marketMessages')]
    private ?MarketOrders $orders = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $priority = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    const string PRIORITY_HIGH = 'high';

    const string PRIORITY_MEDIUM = 'medium';

    const string PRIORITY_LOW = 'low';

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->priority = self::PRIORITY_LOW;
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
     * @return MarketCustomer|null
     */
    public function getCustomer(): ?MarketCustomer
    {
        return $this->customer;
    }

    /**
     * @param MarketCustomer|null $customer
     * @return $this
     */
    public function setCustomer(?MarketCustomer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return MarketProduct|null
     */
    public function getProduct(): ?MarketProduct
    {
        return $this->product;
    }

    /**
     * @param MarketProduct|null $product
     * @return $this
     */
    public function setProduct(?MarketProduct $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeImmutable|null $updated_at
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     * @param \DateTimeInterface|null $deleted_at
     * @return $this
     */
    public function setDeletedAt(?\DateTimeInterface $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @param string|null $priority
     * @return $this
     */
    public function setPriority(?string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return self|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param MarketMessage|null $parent
     * @return $this
     */
    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getMarketMessages(): Collection
    {
        return $this->marketMessages;
    }

    /**
     * @param MarketMessage $marketMessage
     * @return $this
     */
    public function addMarketMessage(self $marketMessage): static
    {
        if (!$this->marketMessages->contains($marketMessage)) {
            $this->marketMessages->add($marketMessage);
            $marketMessage->setParent($this);
        }

        return $this;
    }

    /**
     * @param MarketMessage $marketMessage
     * @return $this
     */
    public function removeMarketMessage(self $marketMessage): static
    {
        if ($this->marketMessages->removeElement($marketMessage)) {
            // set the owning side to null (unless already changed)
            if ($marketMessage->getParent() === $this) {
                $marketMessage->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return MarketOrders|null
     */
    public function getOrders(): ?MarketOrders
    {
        return $this->orders;
    }

    /**
     * @param MarketOrders|null $orders
     * @return $this
     */
    public function setOrders(?MarketOrders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }
}
