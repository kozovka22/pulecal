<?php
declare(strict_types=1);
namespace Pulecal\Service\Service;

use Pulecal\Service\Entity\Calendar;
use Pulecal\Service\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pulecal\Service\Entity\User;

/**
 * @property CalendarRepository $repository Performs DB operations on calendar
 * @property EntityManagerInterface $em Creates and edits entity instances
 */
class CalendarService extends AbstractService {
    private EntityManagerInterface $em;
    private CalendarRepository $repository;
    public function __construct(CalendarRepository $repository, EntityManagerInterface $em) {
        $this->repository = $repository;
        $this->em = $em;
    }
    public function getRepository() {
        return $this->repository;
    }
    public function newCalendar(string $name, User $owner, bool $private = true, bool $active = true, ?string $description = null, ?string $adminDescription = null): Calendar {
        $calendar = (new Calendar())
            ->setOwner($owner)
            ->setName($name)
            ->setPrivate($private)
            ->setActive($active)
            ->setDescription($description)
            ->setAdminDescription($adminDescription)
        ;
        $this->em->persist($calendar);
        $this->em->flush();

        return $calendar;
    }
    public function removeUserFromCalendar(Calendar $calendar, User $user) {
        $calendar->removeUser($user);
        $this->em->persist($calendar);
        $this->em->flush();
    }
    public function verifyOwnership(Calendar $calendar, User $user): bool {
        if ($calendar->getOwner() === $user) {
            return true;
        }
        if (in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_ROOT', $user->getRoles(), true)) {
            return true;
        }
        return false;
    }
    public function editCalendar(Calendar $calendar, string $name, bool $private, ?string $description = null, ?string $adminDescription = null): Calendar {
        $calendar->setName($name);
        $calendar->setPrivate($private);
        $calendar->setDescription($description);
        $calendar->setAdminDescription($adminDescription);
        foreach($calendar->getUsers() as $user) {
            $calendar->removeUser($user);
        }
        $this->em->persist($calendar);
        $this->em->flush();
        return $calendar;
    }
    public function shareWithUser(Calendar $calendar, User $user): Calendar {
        if($calendar->isPrivate()) return $calendar;
        $calendar->addUser($user);
        $this->em->persist($calendar);
        $this->em->flush();
        return $calendar;
    }
    public function unshareWithUser(Calendar $calendar, User $user): Calendar {
        $calendar->removeUser($user);
        $this->em->persist($calendar);
        $this->em->flush();
        return $calendar;
    }
    public function purgeCalendar(Calendar $calendar) {
        foreach($calendar->getUsers() as $user) {
            $calendar->removeUser($user);
        }
        $calendar->setActive(false);
        $this->em->persist($calendar);
        $this->em->flush();
    }
}