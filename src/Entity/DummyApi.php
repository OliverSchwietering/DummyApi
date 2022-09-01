<?php

namespace App\Entity;

use App\Repository\DummyApiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: DummyApiRepository::class)]
class DummyApi
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'dummyApis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'dummyApi', targetEntity: DummyApiEndpoint::class, orphanRemoval: true)]
    private Collection $dummyApiEndpoints;

    #[ORM\OneToMany(mappedBy: 'dummyApi', targetEntity: DummyApiHeader::class, orphanRemoval: true)]
    private Collection $dummyApiHeaders;

    public function __construct()
    {
        $this->dummyApiEndpoints = new ArrayCollection();
        $this->dummyApiHeaders = new ArrayCollection();
    }

    public function getId(): ?string
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, DummyApiEndpoint>
     */
    public function getDummyApiEndpoints(): Collection
    {
        return $this->dummyApiEndpoints;
    }

    public function addDummyApiEndpoint(DummyApiEndpoint $dummyApiEndpoint): self
    {
        if (!$this->dummyApiEndpoints->contains($dummyApiEndpoint)) {
            $this->dummyApiEndpoints[] = $dummyApiEndpoint;
            $dummyApiEndpoint->setDummyApi($this);
        }

        return $this;
    }

    public function removeDummyApiEndpoint(DummyApiEndpoint $dummyApiEndpoint): self
    {
        if ($this->dummyApiEndpoints->removeElement($dummyApiEndpoint)) {
            // set the owning side to null (unless already changed)
            if ($dummyApiEndpoint->getDummyApi() === $this) {
                $dummyApiEndpoint->setDummyApi(null);
            }
        }

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
            $dummyApiHeader->setDummyApi($this);
        }

        return $this;
    }

    public function removeDummyApiHeader(DummyApiHeader $dummyApiHeader): self
    {
        if ($this->dummyApiHeaders->removeElement($dummyApiHeader)) {
            // set the owning side to null (unless already changed)
            if ($dummyApiHeader->getDummyApi() === $this) {
                $dummyApiHeader->setDummyApi(null);
            }
        }

        return $this;
    }
}
