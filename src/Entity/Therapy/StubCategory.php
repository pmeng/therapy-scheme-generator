<?php

namespace App\Entity\Therapy;

use App\Repository\Therapy\StubCategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StubCategoryRepository::class)]
class StubCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $shortName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $reportName;

    #[ORM\Column(type: "integer")]
    private int $categoryOrder;

    #[ORM\OneToMany(targetEntity: Stub::class, mappedBy: "category")]
    private Collection $stubs;

    public function __construct()
    {
        $this->stubs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getReportName(): ?string
    {
        return $this->reportName;
    }

    public function setReportName(?string $reportName): self
    {
        $this->reportName = $reportName;

        return $this;
    }

    public function getCategoryOrder(): ?int
    {
        return $this->categoryOrder;
    }

    public function setCategoryOrder(int $categoryOrder): self
    {
        $this->categoryOrder = $categoryOrder;

        return $this;
    }

    public function getStubs(): Collection
    {
        return $this->stubs;
    }

    public function addStub(Stub $stub): self
    {
        if (!$this->stubs->contains($stub)) {
            $this->stubs[] = $stub;
            $stub->setCategory($this);
        }

        return $this;
    }

    public function removeStub(Stub $stub): self
    {
        if ($this->stubs->removeElement($stub)) {
            // set the owning side to null (unless already changed)
            if ($stub->getCategory() === $this) {
                $stub->setCategory(null);
            }
        }

        return $this;
    }
}
