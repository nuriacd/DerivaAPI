<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'restaurant')]
    private Collection $employees;

    #[ORM\Column(length: 255)]
    private ?string $deliveryCity = null;

    /**
     * @var Collection<int, RestaurantIngredient>
     */
    #[ORM\OneToMany(targetEntity: RestaurantIngredient::class, mappedBy: 'restaurant_id', orphanRemoval: true)]
    private Collection $ingridients;

    /**
     * @var Collection<int, RestaurantDrink>
     */
    #[ORM\OneToMany(targetEntity: RestaurantDrink::class, mappedBy: 'restaurant_id', orphanRemoval: true)]
    private Collection $drinks;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->ingridients = new ArrayCollection();
        $this->drinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setRestaurant($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getRestaurant() === $this) {
                $employee->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDeliveryCity(): ?string
    {
        return $this->deliveryCity;
    }

    public function setDeliveryCity(string $deliveryCity): static
    {
        $this->deliveryCity = $deliveryCity;

        return $this;
    }

    /**
     * @return Collection<int, RestaurantIngredient>
     */
    public function getIngridients(): Collection
    {
        return $this->ingridients;
    }

    public function addIngridient(RestaurantIngredient $ingridient): static
    {
        if (!$this->ingridients->contains($ingridient)) {
            $this->ingridients->add($ingridient);
            $ingridient->setRestaurantId($this);
        }

        return $this;
    }

    public function removeIngridient(RestaurantIngredient $ingridient): static
    {
        if ($this->ingridients->removeElement($ingridient)) {
            // set the owning side to null (unless already changed)
            if ($ingridient->getRestaurantId() === $this) {
                $ingridient->setRestaurantId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RestaurantDrink>
     */
    public function getDrinks(): Collection
    {
        return $this->drinks;
    }

    public function addDrink(RestaurantDrink $drink): static
    {
        if (!$this->drinks->contains($drink)) {
            $this->drinks->add($drink);
            $drink->setRestaurantId($this);
        }

        return $this;
    }

    public function removeDrink(RestaurantDrink $drink): static
    {
        if ($this->drinks->removeElement($drink)) {
            // set the owning side to null (unless already changed)
            if ($drink->getRestaurantId() === $this) {
                $drink->setRestaurantId(null);
            }
        }

        return $this;
    }
}
