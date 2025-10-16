<?php

namespace App\Entity;

use App\Repository\CakeOrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CakeOrderRepository::class)]
class CakeOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity_cake = null;

    #[ORM\ManyToOne(inversedBy: 'cakeOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $orders = null;

    #[ORM\ManyToOne(inversedBy: 'cakeOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cakes $cake = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantityCake(): ?int
    {
        return $this->quantity_cake;
    }

    public function setQuantityCake(int $quantity_cake): static
    {
        $this->quantity_cake = $quantity_cake;

        return $this;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(?Orders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    public function getCake(): ?Cakes
    {
        return $this->cake;
    }

    public function setCake(?Cakes $cake): static
    {
        $this->cake = $cake;

        return $this;
    }
}
