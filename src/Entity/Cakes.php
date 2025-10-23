<?php

namespace App\Entity;

use App\Repository\CakesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CakesRepository::class)]
class Cakes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'cakes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $category = null;

    /**
     * @var Collection<int, Reviews>
     */
    #[ORM\OneToMany(targetEntity: Reviews::class, mappedBy: 'cake')]
    private Collection $reviews;

    /**
     * @var Collection<int, CakeOrder>
     */
    #[ORM\OneToMany(targetEntity: CakeOrder::class, mappedBy: 'cake')]
    private Collection $cakeOrders;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->cakeOrders = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Reviews>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCake($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCake() === $this) {
                $review->setCake(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CakeOrder>
     */
    public function getCakeOrders(): Collection
    {
        return $this->cakeOrders;
    }

    public function addCakeOrder(CakeOrder $cakeOrder): static
    {
        if (!$this->cakeOrders->contains($cakeOrder)) {
            $this->cakeOrders->add($cakeOrder);
            $cakeOrder->setCake($this);
        }

        return $this;
    }

    public function removeCakeOrder(CakeOrder $cakeOrder): static
    {
        if ($this->cakeOrders->removeElement($cakeOrder)) {
            // set the owning side to null (unless already changed)
            if ($cakeOrder->getCake() === $this) {
                $cakeOrder->setCake(null);
            }
        }

        return $this;
    }

    /**
     * Calcule la moyenne des notes des avis pour ce gâteau
     * 
     * @return float La moyenne des notes (0.0 si aucun avis ou aucune note valide)
     */
    public function getAverageRating(): float
    {
        $reviews = $this->getReviews();
        
        // Si aucun avis, retourner 0
        if ($reviews->isEmpty()) {
            return 0.0;
        }
        
        $validNotes = [];
        
        // Collecter toutes les notes valides (non null)
        foreach ($reviews as $review) {
            $note = $review->getNote();
            if ($note !== null && $note > 0) {
                $validNotes[] = (int) $note;
            }
        }
        
        // Si aucune note valide, retourner 0
        if (empty($validNotes)) {
            return 0.0;
        }
        
        // Calculer la moyenne et arrondir à 1 décimale
        $average = array_sum($validNotes) / count($validNotes);
        
        return (float) round($average, 1);
    }
}
