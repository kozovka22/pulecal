<?php
declare(strict_types=1);
namespace Pulecal\Service\Service;

use Doctrine\ORM\EntityManagerInterface;
use Pulecal\Service\Entity\User;
use Pulecal\Service\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @property UserRepository $repository Performs DB operations on User
 * @property EntityManagerInterface $em Creates and edits entity instances
 * @property UserPasswordHasherInterface $ph Hashes passwords (bruh)
 */
class UserService extends AbstractService {
    private UserRepository $repository;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $ph;

    public function __construct(UserRepository $repository, EntityManagerInterface $em, UserPasswordHasherInterface $ph)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->ph = $ph;
    }

    public function getRepository() {
        return $this->repository;
    }
    public function editUser(User $user, string $username, string $email) : User {
        $user->setUsername($username)->setEmail($email);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }
    public function newUser(string $username, string $email, string $password): User {
        $user = (new User())->setUsername($username)->setEmail($email);
        $hashedPw = $this->ph->hashPassword($user, $password);
        $user->setPassword($hashedPw);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }
    public function deleteUser(User $user): void {
        foreach($user->getCalendars() as $calendar) {
            $calendar->removeUser($user);
        }
        foreach($user->getOwnedEvents() as $event) {
            $this->em->remove($event);
        }
        foreach($user->getOwnedCalendars() as $ownedCalendar) {
            $this->em->remove($ownedCalendar);
        }
        $this->em->remove($user);
        $this->em->flush();
    }
}