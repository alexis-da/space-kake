<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $total_price = 0.0;

    #[ORM\Column]
    private ?bool $is_paid = false;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Clients $client = null;

    /**
     * @var Collection<int, CakeOrder>
     */
    #[ORM\OneToMany(targetEntity: CakeOrder::class, mappedBy: 'orders', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $cakeOrders;

    public function __construct()
    {
        $this->cakeOrders = new ArrayCollection();
        $this->total_price = 0.0;
        $this->is_paid = false;
    }

    public function getId(): ?int { return $this->id; }

    public function getTotalPrice(): ?float { return $this->total_price; }
    public function setTotalPrice(float $total_price): static { $this->total_price = $total_price; return $this; }

    public function isPaid(): ?bool { return $this->is_paid; }
    public function setIsPaid(bool $is_paid): static { $this->is_paid = $is_paid; return $this; }

    public function getClient(): ?Clients { return $this->client; }
    public function setClient(?Clients $client): static { $this->client = $client; return $this; }

    /**
     * @return Collection<int, CakeOrder>
     */
    public function getCakeOrders(): Collection { return $this->cakeOrders; }

    // Ajoute l'objet s'il n'est pas déjà présent (par identité de l'entité CakeOrder)
    public function addCakeOrder(CakeOrder $cakeOrder): static
    {
        // Vérification si le gâteau existe déjà
        foreach ($this->cakeOrders as $existingOrder) {
            if ($existingOrder->getCake() === $cakeOrder->getCake()) {
                $existingOrder->setQuantityCake(
                    $existingOrder->getQuantityCake() + $cakeOrder->getQuantityCake()
                );
                return $this;
            }
        }

        $this->cakeOrders->add($cakeOrder);
        $cakeOrder->setOrders($this);

        return $this;
    }

    public function removeCakeOrder(CakeOrder $cakeOrder): static
    {
        if ($this->cakeOrders->removeElement($cakeOrder)) {
            if ($cakeOrder->getOrders() === $this) {
                $cakeOrder->setOrders(null);
            }
        }
        return $this;
    }
}
