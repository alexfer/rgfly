<?php declare(strict_types=1);

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\StoreOptionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreOptionsRepository::class)]
class StoreOptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'storeOptions', cascade: ['persist', 'remove'])]
    private ?Store $store = null;

    #[ORM\Column(nullable: true)]
    private ?int $backup_schedule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): static
    {
        $this->store = $store;

        return $this;
    }

    public function getBackupSchedule(): ?int
    {
        return $this->backup_schedule;
    }

    public function setBackupSchedule(?int $backup_schedule): static
    {
        $this->backup_schedule = $backup_schedule;

        return $this;
    }
}
