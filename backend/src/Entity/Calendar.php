<?php 
namespace Pulecal\Service\Entity;
use Doctrine\ORM\Mapping as ORM;
use Pulecal\Service\Entity\Event;
use Pulecal\Service\Repository\CalendarRepository;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adminDescription;

    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'calendars')]
    #[ORM\JoinTable(name: 'calendar_event')]
    private $events = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'calendars')]
    #[ORM\JoinTable(name: 'user_calendar')]
    private $users = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn()]
    private User $owner;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $private = true;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $deactivatedAt = null;

    public function __construct(?User $owner = null) {
        if ($owner !== null) {
            $this->owner = $owner;
        }
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getEvents() {
        return $this->events;
    }

    public function setEvents($events): self {
        $this->events = $events;
        return $this;
    }

    public function addEvent($event): self {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addCalendar($this);
        }
        return $this;
    }

    public function removeEvent($event): self {
        if ($this->events->removeElement($event)) {
            if ($event->getCalendar() === $this) {
                $event->removeCalendar($this);
            }
        }
        return $this;
    }

    public function isPrivate(): bool {
        return $this->private;
    }

    public function setPrivate(bool $private): self {
        $this->private = $private;
        return $this;
    }

    public function isActive(): bool {
        return $this->active;
    }

    public function setActive(bool $active): self {
        $this->active = $active;
        if (!$active) {
            $this->deactivatedAt = new \DateTime();
        }
        return $this;
    }

    public function getDeactivatedAt(): ?\DateTime {
        return $this->deactivatedAt;
    }

    public function setDeactivatedAt(?\DateTime $deactivatedAt): self {
        $this->deactivatedAt = $deactivatedAt;
        return $this;
    }

    public function getOwner(): ?User {
        return $this->owner;
    }

    public function setOwner(?User $owner): self {
        $this->owner = $owner;
        return $this;
    }

    public function getUsers() {
        return $this->users;
    }

    public function setUsers($users): self {
        $this->users = $users;
        return $this;
    }

    public function addUser(User $user): self {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCalendar($this);
        }
        return $this;
    }

    public function removeUser(User $user): self {
        if ($this->users->removeElement($user)) {
            $user->removeCalendar($this);
        }
        return $this;
    }

    public function __toString(): string {
        return $this->name ?? 'Calendar';
    }

    public function toArray(): array {
        $userIds = [];
        foreach ($this->getUsers() as $user) {
            $userIds[] = $user->getId();
        }
        $eventIds = [];
        foreach ($this->getEvents() as $event) {
            $eventIds[] = $event->getId();
        }
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "description" => $this->getDescription(),
            "adminDescription" => $this->getAdminDescription(),
            "ownerId" => $this->getOwner()?->getId(),
            "userIds" => $userIds,
            "eventIds" => $eventIds,
            "private" => $this->isPrivate(),
            "active" => $this->isActive(),
            "deactivatedAt" => $this->getDeactivatedAt()?->getTimestamp()
        ];
    }
}