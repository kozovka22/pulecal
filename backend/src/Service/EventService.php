<?php
declare(strict_types=1);
namespace Pulecal\Service\Service;

use Doctrine\ORM\EntityManagerInterface;
use Pulecal\Service\Entity\Calendar;
use Pulecal\Service\Entity\Event;
use Pulecal\Service\Entity\User;
use Pulecal\Service\Repository\EventRepository;

/**
 * @property EventRepository $repository Performs DB operations on Event
 * @property EntityManagerInterface $em Creates and edits entity instances
 */
class EventService extends AbstractService {
    private EventRepository $eventRepository;
    private EntityManagerInterface $em;
    public function __construct(EventRepository $eventRepository, EntityManagerInterface $em)
    {
        $this->eventRepository = $eventRepository;
        $this->em = $em;
    }
    public function getRepository() {
        return $this->eventRepository;
    }
    public function newEvent(Calendar $calendar, User $owner, string $name, \DateTime $startTime, \DateTime $endTime, bool $private = true, ?string $description = null, bool $repeats = false, ?string $repeatInterval = null, ?string $adminDescription = null, string $status = 'waiting'): Event {
        $event = (new Event())
            ->addCalendar($calendar)
            ->setOwner($owner)
            ->setName($name)
            ->setDescription($description)
            ->setAdminDescription($adminDescription)
            ->setStartTime($startTime)
            ->setEndTime($endTime)
            ->setRepeats($repeats)
            ->setRepeatInterval($repeatInterval)
            ->setStatus($status)
        ;
        $this->em->persist($event);
        $this->em->flush();
        return $event;
    }
    public function verifyOwnership(Event $event, User $user): bool {
        if($event->getOwner()===$user) return true;
        return false;
    }
    public function deleteEvent(Event $event, Calendar $calendar) {
        $calendar->removeEvent($event);
        $this->em->persist($event);
        $this->em->flush();
    }

    public function editEvent(Event $event, string $name, \DateTime $start, \DateTime $end, bool $repeats, ?string $repeatInterval, bool $private, ?string $description = null, ?string $adminDescription = null): Event {
        $event
            ->setName($name)
            ->setStartTime($start)
            ->setEndTime($end)
            ->setRepeats($repeats)
            ->setRepeatInterval($repeatInterval)
            ->setPrivate($private)
            ->setDescription($description)
            ->setAdminDescription($adminDescription)
        ;
        $this->em->persist($event);
        $this->em->flush();
        return $event;
    }

    public function save(Event $event): void {
        $this->em->persist($event);
        $this->em->flush();
    }

    public function linkEvent(Event $event, Calendar $calendar, User $user): Event {
        if ($this->verifyOwnership($event, $user) && ($calendar->getOwner() === $user || $calendar->getUsers()->contains($user))) {
            $event->addCalendar($calendar);
            $this->em->persist($event);
            $this->em->flush();
        }
        return $event;
    }

    public function unlinkEvent(Event $event, Calendar $calendar, User $user): Event {
        if ($this->verifyOwnership($event, $user) && ($calendar->getOwner() === $user || $calendar->getUsers()->contains($user))) {
            $event->removeCalendar($calendar);
            $this->em->persist($event);
            $this->em->flush();
        }
        return $event;
    }

    public function purgeEvent(Event $event): void {
        $this->em->remove($event);
        $this->em->flush();
    }
}
