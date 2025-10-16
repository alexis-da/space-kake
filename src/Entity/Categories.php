<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    /**
     * @var Collection<int, Cakes>
     */
    #[ORM\OneToMany(targetEntity: Cakes::class, mappedBy: 'category')]
    private Collection $cakes;

    public function __construct()
    {
        $this->cakes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Cakes>
     */
    public function getCakes(): Collection
    {
        return $this->cakes;
    }

    public function addCake(Cakes $cake): static
    {
        if (!$this->cakes->contains($cake)) {
            $this->cakes->add($cake);
            $cake->setCategory($this);
        }

        return $this;
    }

    public function removeCake(Cakes $cake): static
    {
        if ($this->cakes->removeElement($cake)) {
            // set the owning side to null (unless already changed)
            if ($cake->getCategory() === $this) {
                $cake->setCategory(null);
            }
        }

        return $this;
    }
}
