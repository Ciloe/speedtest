<?php

namespace App\Entity;

use App\Repository\LoggerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoggerRepository::class)
 */
class Logger
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="launched_at", type="datetime_immutable")
     */
    private $launchedAt;

    /**
     * @ORM\Column(type="float")
     */
    private $upload;

    /**
     * @ORM\Column(type="float")
     */
    private $download;

    /**
     * @ORM\Column(type="float")
     */
    private $latency;

    /**
     * @ORM\Column(type="json")
     */
    private $bytes = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLaunchedAt(): ?\DateTimeImmutable
    {
        return $this->launchedAt;
    }

    public function setLaunchedAt(\DateTimeImmutable $launchedAt): self
    {
        $this->launchedAt = $launchedAt;

        return $this;
    }

    public function getUpload(): ?float
    {
        return $this->upload;
    }

    public function setUpload(float $upload): self
    {
        $this->upload = $upload;

        return $this;
    }

    public function getDownload(): ?float
    {
        return $this->download;
    }

    public function setDownload(float $download): self
    {
        $this->download = $download;

        return $this;
    }

    public function getLatency(): ?float
    {
        return $this->latency;
    }

    public function setLatency(float $latency): self
    {
        $this->latency = $latency;

        return $this;
    }

    public function getBytes(): ?array
    {
        return $this->bytes;
    }

    public function setBytes(array $bytes): self
    {
        $this->bytes = $bytes;

        return $this;
    }
}
