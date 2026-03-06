<?php
declare(strict_types=1);
namespace Pulecal\Service\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pulecal\Service\Entity\Event;

class EventRepository extends ServiceEntityRepository {
        public function __construct(ManagerRegistry $registry)
    {
        return parent::__construct($registry, Event::class);
    }
}