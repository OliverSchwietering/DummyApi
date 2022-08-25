<?php

namespace App\Entity;

use App\Repository\DummyApiHeaderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DummyApiHeaderRepository::class)]
class DummyApiHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'dummyApiHeaders')]
    private ?DummyApi $dummyApi = null;

    #[ORM\ManyToOne(inversedBy: 'dummyApiHeaders')]
    private ?DummyApiEndpoint $dummyApiEndpoint = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDummyApi(): ?DummyApi
    {
        return $this->dummyApi;
    }

    public function setDummyApi(?DummyApi $dummyApi): self
    {
        $this->dummyApi = $dummyApi;

        return $this;
    }

    public function getDummyApiEndpoint(): ?DummyApiEndpoint
    {
        return $this->dummyApiEndpoint;
    }

    public function setDummyApiEndpoint(?DummyApiEndpoint $dummyApiEndpoint): self
    {
        $this->dummyApiEndpoint = $dummyApiEndpoint;

        return $this;
    }
}
