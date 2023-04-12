<?php

namespace App\Entity;

use App\Repository\UrlsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlsRepository::class)]
#[ORM\Index(name: 'hash_idx', columns: ['hash'])]
class Urls
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $url = null;

    #[ORM\Column(type: Types::BINARY,unique: true)]
    private $hash = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash): self
    {
        $this->hash = $hash;

        return $this;
    }
}
