<?php
declare(strict_types=1);
namespace Pulecal\Service\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Pulecal\Service\Entity\{User};
use Pulecal\Service\Repository\EventRepository;

use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @property User $owner The owner of the event
 * @property User $user User added to the event
 * @property string $name Name given to the event by its owner
 * @property \DateTime $startTime Start time of the event
 * @property \DateTime $endTime End time of the event
 * @property bool $repeats Does the event repeat
 */
class EventFilter {
    private const FILTER_OWNER = 0b0000001;
    private const FILTER_USER = 0b0000010;
    private const FILTER_NAME = 0b0001000;
    private const FILTER_START_TIME = 0b0010000;
    private const FILTER_END_TIME = 0b0100000;
    private const FILTER_REPEATS = 0b1000000;

    private User|null $owner;
    private User|null $user;
    private string $name;
    private ?\DateTime $startTime;
    private ?\DateTime $endTime;
    private ?bool $repeats;
    private int $filterMask = 0;

    public function __construct(EventRepository $repository, array $params)
    {
        
        $this->owner = $repository->find((int)($params["ownerId"] ?? -1));
        $this->user = $repository->find((int)($params["userId"] ?? -1));
        $this->name = $params["name"] ?? '';
        $this->startTime = $params["start"] ?? null;
        $this->endTime = $params["end"] ?? null;
        $this->repeats = $params["repeats"] ?? null;
        $this->buildFilterMask($params);
    }

    private function buildFilterMask(array $params): void {
        foreach(
            [
                "ownerId" => self::FILTER_OWNER, 
                "userId" => self::FILTER_USER, 
                "name" => self::FILTER_NAME,
                "startTime" => self::FILTER_START_TIME,
                "endTime" => self::FILTER_END_TIME,
                "repeats" => self::FILTER_REPEATS
            ] as $name => $filter
        ) {
            if(isset($params[$name]) && $params[$name] !== null) $this->filterMask |= $filter;
        }
    }

    public function toCriteria(QueryBuilder $queryBuilder): QueryBuilder {
        $criteria = Criteria::create();
        $expr = $criteria::expr();
        $alias = $queryBuilder->getRootAliases()[0];
        if($this->filterMask & self::FILTER_OWNER) {
            $criteria->andWhere($expr->eq('owner', $this->owner));
        }
        if($this->filterMask & self::FILTER_USER) {
            $queryBuilder->innerJoin("$alias.users", "uf")
                ->andWhere("uf = :usr")
                ->setParameter("usr", $this->user);
        }
        if ($this->filterMask & self::FILTER_NAME) {
            $criteria->andWhere($expr->contains("name", $this->name));
        }
        if ($this->filterMask & self::FILTER_START_TIME) {
            $criteria->andWhere($expr->gte('startTime', $this->startTime));
        } 
        if ($this->filterMask & self::FILTER_END_TIME) {
            $criteria->andWhere($expr->lte('endTime', $this->endTime));
        }
        if ($this->filterMask & self::FILTER_REPEATS) {
            $criteria->andWhere($expr->eq('repeats', $this->repeats));
        }
        $queryBuilder->addCriteria($criteria);
        return $queryBuilder;
    }
}