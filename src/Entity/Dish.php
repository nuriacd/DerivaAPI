<?php

namespace App\Entity;

use App\Repository\DishRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish extends Product
{
    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 5000)]
    private ?string $recipe = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getRecipe(): ?string
    {
        return $this->recipe;
    }

    public function setRecipe(string $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }
}
