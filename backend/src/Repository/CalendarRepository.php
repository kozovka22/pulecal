<?php
declare(strict_types=1);
namespace Pulecal\Service\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pulecal\Service\Entity\Calendar;

class CalendarRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        return parent::__construct($registry, Calendar::class);
    }
}