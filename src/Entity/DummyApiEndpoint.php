<?php

namespace App\Entity;

use App\Repository\DummyApiEndpointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: DummyApiEndpointRepository::class)]
class DummyApiEndpoint
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $allowedMethods = [];

    #[ORM\Column(length: 255)]
    private ?string $contentType = null;

    #[ORM\ManyToOne(inversedBy: 'dummyApiEndpoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DummyApi $dummyApi = null;

    #[ORM\OneToMany(mappedBy: 'dummyApiEndpoint', targetEntity: DummyApiHeader::class)]
    private Collection $dummyApiHeaders;

    public function __construct()
    {
        $this->dummyApiHeaders = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }

    public function setAllowedMethods(?array $allowedMethods): self
    {
        $this->allowedMethods = $allowedMethods;

        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

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

    /**
     * @return Collection<int, DummyApiHeader>
     */
    public function getDummyApiHeaders(): Collection
    {
        return $this->dummyApiHeaders;
    }

    public function addDummyApiHeader(DummyApiHeader $dummyApiHeader): self
    {
        if (!$this->dummyApiHeaders->contains($dummyApiHeader)) {
            $this->dummyApiHeaders[] = $dummyApiHeader;
            $dummyApiHeader->setDummyApiEndpoint($this);
        }

        return $this;
    }

    public function removeDummyApiHeader(DummyApiHeader $dummyApiHeader): self
    {
        if ($this->dummyApiHeaders->removeElement($dummyApiHeader)) {
            // set the owning side to null (unless already changed)
            if ($dummyApiHeader->getDummyApiEndpoint() === $this) {
                $dummyApiHeader->setDummyApiEndpoint(null);
            }
        }

        return $this;
    }
}
