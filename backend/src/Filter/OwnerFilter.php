<?php
declare(strict_types=1);

namespace Pulecal\Service\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Pulecal\Service\Entity\Event;
use Pulecal\Service\Entity\Calendar;

class OwnerFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->hasAssociation('owner')) {
            return sprintf('%s.owner_id = %s', $targetTableAlias, $this->getParameter('userId'));
        }

        if ($targetEntity->hasAssociation('user')) {
            return sprintf('%s.user_id = %s', $targetTableAlias, $this->getParameter('userId'));
        }

        return '';
    }
}
