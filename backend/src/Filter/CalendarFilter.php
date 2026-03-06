<?php
declare(strict_types=1);
namespace Pulecal\Service\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Pulecal\Service\Entity\User;
use Pulecal\Service\Repository\UserRepository;

use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @property User $owner The owner of the calendar
 * @property User $user User added to the calendar
 * @property bool $active Is calendar marked as active or scheduled for deletion
 * @property string $name Name given to the calendar by its owner
 */
class CalendarFilter {
    private const FILTER_OWNER = 0b0001;
    private const FILTER_USER = 0b0010;
    private const FILTER_ACTIVE = 0b0100;
    private const FILTER_NAME = 0b1000;

    private User|null $owner;
    private User|null $user;
    private bool $active;
    private string $name;
    private int $filterMask = 0;
    public function __construct(UserRepository $repository, array $params)
    {
        
        $this->owner = $repository->find((int)($params["ownerId"] ?? -1));
        $this->user = $repository->find((int)($params["userId"] ?? -1));
        $this->active = $params["active"] ?? false;
        $this->name = $params["name"] ?? '';
        $this->buildFilterMask($params);
    }

    private function buildFilterMask(array $params): void {
        foreach(
            [
                "ownerId" => self::FILTER_OWNER, 
                "userId" => self::FILTER_USER, 
                "active" => self::FILTER_ACTIVE, 
                "name" => self::FILTER_NAME
            ] as $name => $filter
        ) {
            if(isset($params[$name]) && $params[$name] !== null) $this->filterMask |= $filter;
        }
    }

    public function toCriteria(QueryBuilder $queryBuilder): QueryBuilder {
        $criteria = Criteria::create();
        $expr = $criteria::expr();
        $alias = $queryBuilder->getRootAliases()[0];

        if (($this->filterMask & self::FILTER_OWNER) && ($this->filterMask & self::FILTER_USER)) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                    "$alias.owner = :owner",
                    ":usr MEMBER OF $alias.users"
                ))
                ->setParameter("owner", $this->owner)
                ->setParameter("usr", $this->user);
        } else {
            if ($this->filterMask & self::FILTER_OWNER) {
                $criteria->andWhere($expr->eq('owner', $this->owner));
            }
            if ($this->filterMask & self::FILTER_USER) {
                $queryBuilder->innerJoin("$alias.users", "uf")
                    ->andWhere("uf = :usr")
                    ->setParameter("usr", $this->user);
            }
        }

        if ($this->filterMask & self::FILTER_ACTIVE) {
            $criteria->andWhere($expr->eq('active', $this->active));
        }
        if ($this->filterMask & self::FILTER_NAME) {
            $criteria->andWhere($expr->contains("name", $this->name));
        }

        $queryBuilder->addCriteria($criteria);
        return $queryBuilder;
    }
}