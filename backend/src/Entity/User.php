<?php
namespace Pulecal\Service\Entity;
use Doctrine\ORM\Mapping as ORM;
use Pulecal\Service\Entity\Calendar;
use Pulecal\Service\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_USERNAME', columns: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $username;
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password = null;
    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\ManyToMany(targetEntity: Calendar::class, mappedBy: 'users')]
    private $calendars = null;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'owner')]
    private $ownedEvents;

    #[ORM\OneToMany(targetEntity: Calendar::class, mappedBy: 'owner')]
    private $ownedCalendars = null;

    #[ORM\OneToMany(targetEntity: ApiKey::class, mappedBy: 'user', cascade: ['remove'])]
    private $apiKeys;
    
    public function __construct() {
        $this->calendars = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ownedEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ownedCalendars = new \Doctrine\Common\Collections\ArrayCollection();
        $this->apiKeys = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getId(): int {
        return $this->id;
    }
    public function getUsername(): string {
        return $this->username;
    }
    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }
    public function getEmail(): string {
        return $this->email;
    }
    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }
    public function getCalendars() {
        return $this->calendars;
    }
    public function setCalendars($calendars): self {
        $this->calendars = $calendars;
        return $this;
    }
    public function addCalendar($calendar): self {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars[] = $calendar;
        }
        return $this;
    }
    public function removeCalendar($calendar): self {
        $this->calendars->removeElement($calendar);
        return $this;
    }

    public function getOwnedEvents() {
        return $this->ownedEvents;
    }

    public function setOwnedEvents($ownedEvents): self {
        $this->ownedEvents = $ownedEvents;
        return $this;
    }

    public function addOwnedEvent($event): self {
        if (!$this->ownedEvents->contains($event)) {
            $this->ownedEvents[] = $event;
            $event->setOwner($this);
        }
        return $this;
    }

    public function removeOwnedEvent($event): self {
        $this->ownedEvents->removeElement($event);
        return $this;
    }

    public function getOwnedCalendars() {
        return $this->ownedCalendars;
    }

    public function setOwnedCalendars($ownedCalendars): self {
        $this->ownedCalendars = $ownedCalendars;
        return $this;
    }

    public function addOwnedCalendar(Calendar $calendar): self {
        if (!$this->ownedCalendars->contains($calendar)) {
            $this->ownedCalendars[] = $calendar;
            $calendar->setOwner($this);
        }
        return $this;
    }

    public function removeOwnedCalendar(Calendar $calendar): self {
        if ($this->ownedCalendars->removeElement($calendar)) {
            if ($calendar->getOwner() === $this) {
                $calendar->setOwner(null);
            }
        }
        return $this;
    }

    public function __toString(): string {
        return $this->username ?? 'User';
    }

    public function getApiKeys() {
        return $this->apiKeys;
    }

    public function setApiKeys($apiKeys): self {
        $this->apiKeys = $apiKeys;
        return $this;
    }

    public function addApiKey(ApiKey $apiKey): self {
        if (!$this->apiKeys->contains($apiKey)) {
            $this->apiKeys[] = $apiKey;
        }
        return $this;
    }

    public function removeApiKey(ApiKey $apiKey): self {
        $this->apiKeys->removeElement($apiKey);
        return $this;
    }

    public function getRoles(): array {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): self {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {
        // No sensitive data to erase
    }

    public function getUserIdentifier(): string {
        return $this->username;
    }
    public function toArray(): array {
        return [
            "id" => $this->getId(),
            "username" => $this->getUsername(),
            "email" => $this->getEmail()
        ];
    }
}