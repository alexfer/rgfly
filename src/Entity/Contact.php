<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{

    const CONSTRAINTS = [
        'name' => [
            'min' => 3,
            'max' => 200,
        ],
        'subject' => [
            'min' => 10,
            'max' => 200,
        ],
        'message' => [
            'min' => 100,
            'max' => 65535,
        ],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'form.name.not_blank')]
    #[Assert\Length(
                min: self::CONSTRAINTS['name']['min'],
                minMessage: 'form.name.min',
                max: self::CONSTRAINTS['name']['max'],
                maxMessage: 'form.name.max',
        )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
                pattern: "/^[0-9]*$/",
                message: 'form.phone.not_valid',
        )]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
                min: self::CONSTRAINTS['subject']['min'],
                minMessage: 'form.subject.min',
                max: self::CONSTRAINTS['subject']['max'],
                maxMessage: 'form.subject.max',
        )]
    private ?string $subject = null;

    #[ORM\Column(length: 65535)]
    #[Assert\NotBlank(message: 'form.message.not_blank')]
    #[Assert\Length(
                min: self::CONSTRAINTS['message']['min'],
                minMessage: 'form.message.min',
                max: self::CONSTRAINTS['message']['max'],
                maxMessage: 'form.message.max',
        )]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'form.email.not_valid')]
    #[Assert\NotBlank(message: 'form.email.not_blank')]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $created_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

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

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
