<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish extends Product
{
    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 5000)]
    private ?string $recipe = null;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\ManyToMany(targetEntity: Ingredient::class)]
    private Collection $ingredients;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        $this->ingredients->removeElement($ingredient);

        return $this;
    }
}
