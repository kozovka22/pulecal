<?php
declare(strict_types=1);

namespace Pulecal\Service\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Pulecal\Service\Entity\Calendar;
use Pulecal\Service\Entity\Event;
use Pulecal\Service\Entity\User;
use Pulecal\Service\Filter\CalendarFilter;
use Pulecal\Service\Filter\EventFilter;
use Pulecal\Service\Filter\UserFilter;
use Pulecal\Service\Repository\CalendarRepository;
use Pulecal\Service\Repository\EventRepository;
use Pulecal\Service\Repository\UserRepository;

abstract class AbstractService {
    public function listInstances(  
        UserFilter | CalendarFilter | EventFilter $filter, 
        UserRepository | CalendarRepository | EventRepository $repository
    ): Collection {
        $queryBuilder = $repository->createQueryBuilder("i");
        $queryBuilder->select("i");
        $instances = $filter->toCriteria($queryBuilder)->getQuery()->getResult();
        return $this->forceCollection($instances);
    }

    private function forceCollection(array|Collection $instances): Collection {
        if($instances instanceof Collection) return $instances;
        return new ArrayCollection($instances);
    }

    public function getInstanceById(
        int $id,
        UserRepository | CalendarRepository | EventRepository $repository
    ): Event|User|Calendar | null{
        return $repository->find($id);
    }
}