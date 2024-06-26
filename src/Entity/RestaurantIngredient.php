<?php

namespace App\Entity;

use App\Repository\RestaurantIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantIngredientRepository::class)]
class RestaurantIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'ingridients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ingredient $ingredient_id = null;

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

    public function getRestaurantId(): ?Restaurant
    {
        return $this->restaurant_id;
    }

    public function setRestaurantId(?Restaurant $restaurant_id): static
    {
        $this->restaurant_id = $restaurant_id;

        return $this;
    }

    public function getIngredientId(): ?Ingredient
    {
        return $this->ingredient_id;
    }

    public function setIngredientId(?Ingredient $ingredient_id): static
    {
        $this->ingredient_id = $ingredient_id;

        return $this;
    }
}
