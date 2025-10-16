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
    private ?float $total_price = null;

    #[ORM\Column]
    private ?bool $is_paid = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Clients $client = null;

    /**
     * @var Collection<int, CakeOrder>
     */
    #[ORM\OneToMany(targetEntity: CakeOrder::class, mappedBy: 'orders')]
    private Collection $cakeOrders;

    public function __construct()
    {
        $this->cakeOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->is_paid;
    }

    public function setIsPaid(bool $is_paid): static
    {
        $this->is_paid = $is_paid;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): static
    {
        $this->client = $client;

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
            $cakeOrder->setOrders($this);
        }

        return $this;
    }

    public function removeCakeOrder(CakeOrder $cakeOrder): static
    {
        if ($this->cakeOrders->removeElement($cakeOrder)) {
            // set the owning side to null (unless already changed)
            if ($cakeOrder->getOrders() === $this) {
                $cakeOrder->setOrders(null);
            }
        }

        return $this;
    }
}
