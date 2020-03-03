<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $unique_code;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $words = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqueCode(): ?string
    {
        return $this->unique_code;
    }

    public function setUniqueCode(string $unique_code): self
    {
        $this->unique_code = $unique_code;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getWords(): ?array
    {
        return $this->words;
    }

    public function setWords(?array $words): self
    {
        $this->words = $words;

        return $this;
    }
}
