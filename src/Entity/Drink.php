<?php

namespace App\Entity;

use App\Repository\DrinkRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrinkRepository::class)]
class Drink extends Product
{
    

}
