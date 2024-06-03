<?php

namespace App\Entity;

use App\Repository\RestaurantDrinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantDrinkRepository::class)]
class RestaurantDrink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'drinks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?restaurant $restaurant_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?drink $drink_id = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRestaurantId(): ?restaurant
    {
        return $this->restaurant_id;
    }

    public function setRestaurantId(?restaurant $restaurant_id): static
    {
        $this->restaurant_id = $restaurant_id;

        return $this;
    }

    public function getDrinkId(): ?drink
    {
        return $this->drink_id;
    }

    public function setDrinkId(?drink $drink_id): static
    {
        $this->drink_id = $drink_id;

        return $this;
    }
}
