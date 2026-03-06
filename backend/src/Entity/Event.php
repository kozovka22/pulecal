<?php
namespace Pulecal\Service\Entity;
use Doctrine\ORM\Mapping as ORM;
use Pulecal\Service\Repository\EventRepository;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adminDescription;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $startTime;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $endTime;
    
    #[ORM\ManyToMany(targetEntity: Calendar::class, mappedBy: 'events')]
    private $calendars;

    #[ORM\Column(type: 'boolean')]
    private bool $repeats = false;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $repeatInterval = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownedEvents')]
    #[ORM\JoinColumn()]
    private User $owner;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $private = true;

    #[ORM\Column(type: 'string', length: 25, options: ['default' => 'waiting'])]
    private string $status = 'none';

    public function __construct() {
        $this->calendars = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getCalendars() {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): self {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars[] = $calendar;
            $calendar->addEvent($this);
        }
        return $this;
    }

    public function removeCalendar(Calendar $calendar): self {
        if ($this->calendars->removeElement($calendar)) {
            $calendar->removeEvent($this);
        }
        return $this;
    }

    public function setCalendars($calendars): self {
        $this->calendars = $calendars;
        return $this;
    }
    
    public function getId(): int {
        return $this->id;
    }
    public function getName(): string {
        return $this->name;
    }
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
    public function getDescription(): ?string {
        return $this->description;
    }
    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }
    public function getAdminDescription(): ?string {
        return $this->adminDescription;
    }
    public function setAdminDescription(?string $adminDescription): self {
        $this->adminDescription = $adminDescription;
        return $this;
    }
    public function getStartTime(): \DateTime {
        return $this->startTime;
    }
    public function setStartTime(\DateTime $startTime): self {
        $this->startTime = $startTime;
        return $this;
    }
    public function getEndTime(): \DateTime {
        return $this->endTime;
    }
    public function setEndTime(\DateTime $endTime): self {
        $this->endTime = $endTime;
        return $this;
    }

    public function getRepeats(): bool {
        return $this->repeats;
    }
    public function setRepeats(bool $repeats): self {
        $this->repeats = $repeats;
        return $this;
    }
    public function getRepeatInterval(): ?string {
        return $this->repeatInterval;
    }
    public function setRepeatInterval(?string $repeatInterval): self {
        $this->repeatInterval = $repeatInterval;
        return $this;
    }

    public function getOwner(): User {
        return $this->owner;
    }

    public function setOwner(User $owner): self {
        $this->owner = $owner;
        return $this;
    }

    public function isPrivate(): bool {
        return $this->private;
    }

    public function setPrivate(bool $private): self {
        $this->private = $private;
        return $this;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): self {
        $this->status = $status;
        return $this;
    }

    public function __toString(): string {
        return $this->name ?? 'Event';
    }

    public function toArray(): array {
        $calendarIds = [];
        foreach($this->getCalendars() as $calendar){
            $calendarIds[] = $calendar->getId();
        }
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'adminDescription' => $this->getAdminDescription(),
            'start' => $this->getStartTime()->getTimestamp(),
            'end' => $this->getEndTime()->getTimestamp(),
            'repeats' => $this->getRepeats(),
            'repeatInterval' => $this->getRepeatInterval(),
            'ownerId' => $this->getOwner()?->getId(),
            'calendarIds' => $calendarIds,
            'private' => $this->isPrivate(),
            'status' => $this->getStatus()
        ];
    }
}