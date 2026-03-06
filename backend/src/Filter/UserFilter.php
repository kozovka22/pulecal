<?php
declare(strict_types=1);
namespace Pulecal\Service\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Pulecal\Service\Entity\{Calendar, Event};
use Pulecal\Service\Repository\UserRepository;

use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @property string $username Username of user
 * @property string $email Email of user
 * @property Calendar $calendar Member of calendar
 * @property Event $event Member of event
 */
class UserFilter {
    private const FILTER_USERNAME = 0b0000001;
    private const FILTER_EMAIL = 0b0000010;
    private const FILTER_CALENDAR = 0b0000100;
    private const FILTER_EVENT = 0b0001000;

    private string $username;
    private string $email;
    private int $calendarId;
    private int $eventId;
    private int $filterMask = 0;

    public function __construct(UserRepository $repository, array $params)
    {
        $this->username = $params['username'] ?? '';
        $this->email = $params['email'] ?? '';
        $this->calendarId = (int)($params["calendarId"] ?? -1);
        $this->eventId = (int)($params["eventId"] ?? -1);
        $this->buildFilterMask($params);
    }

    private function buildFilterMask(array $params): void {
        foreach(
            [
                "username" => self::FILTER_USERNAME, 
                "email" => self::FILTER_EMAIL, 
                "calendarId" => self::FILTER_CALENDAR, 
                "eventId" => self::FILTER_EVENT
            ] as $name => $filter
        ) {
            if(isset($params[$name]) && $params[$name] !== null) $this->filterMask |= $filter;
        }
    }

    public function toCriteria(QueryBuilder $queryBuilder): QueryBuilder {
        $criteria = Criteria::create();
        $expr = $criteria::expr();
        $alias = $queryBuilder->getRootAliases()[0];
        if($this->filterMask & self::FILTER_USERNAME) {
            $criteria->andWhere($expr->contains('username', $this->username));
        }
        if($this->filterMask & self::FILTER_EMAIL) {
            $criteria->andWhere($expr->eq('email', $this->email));
        }
        if($this->filterMask & self::FILTER_CALENDAR) {
            $queryBuilder->innerJoin("$alias.calendars", "cs")
                ->andWhere("cs.id = :calId")
                ->setParameter("calId", (int)($this->calendarId ?? -1));
        }
        if($this->filterMask & self::FILTER_EVENT) {
            $queryBuilder->innerJoin("$alias.events", "ev")
                ->andWhere("ev.id = :eveId")
                ->setParameter("eveId", (int)($this->eventId ?? -1));
        }
        $queryBuilder->addCriteria($criteria);
        return $queryBuilder;
    }
}