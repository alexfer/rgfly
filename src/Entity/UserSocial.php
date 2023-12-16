<?php

namespace App\Entity;

use App\Repository\UserSocialRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSocialRepository::class)]
class UserSocial
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userSocial', cascade: ['persist', 'remove'])]
    private ?UserDetails $details = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $facebook_profile = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $twitter_profile = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $instagram_profile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetails(): ?UserDetails
    {
        return $this->details;
    }

    public function setDetails(?UserDetails $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getFacebookProfile(): ?string
    {
        return $this->facebook_profile;
    }

    public function setFacebookProfile(?string $facebook_profile): static
    {
        $this->facebook_profile = $facebook_profile;

        return $this;
    }

    public function getTwitterProfile(): ?string
    {
        return $this->twitter_profile;
    }

    public function setTwitterProfile(?string $twitter_profile): static
    {
        $this->twitter_profile = $twitter_profile;

        return $this;
    }

    public function getInstagramProfile(): ?string
    {
        return $this->instagram_profile;
    }

    public function setInstagramProfile(?string $instagram_profile): static
    {
        $this->instagram_profile = $instagram_profile;

        return $this;
    }
}
