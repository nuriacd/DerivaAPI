<?php

namespace App\Repository;

use App\Entity\Drink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DrinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Drink::class);
    }

    // Puedes agregar métodos específicos para consultas relacionadas con empleados aquí
}
