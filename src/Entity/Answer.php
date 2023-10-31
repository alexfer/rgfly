<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[ORM\Table(name: 'answer')]
class Answer
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false, name: "contact_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Contact $contact = null;

    #[ORM\Column]
    private ?int $contact_id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, name: "user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column(type: Types::TEXT, length: 65535)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $created_at = null;

    public function __construct()
    {
        $this->created_at = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->$user_id = $user_id;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): void
    {
        $this->contact = $contact;
    }

    public function getContactId(): ?int
    {
        return $this->contact_id;
    }

    public function setContactId(int $contact_id): static
    {
        $this->contact_id = $contact_id;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
