<?php
declare(strict_types=1);

namespace Pulecal\Service\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Pulecal\Service\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 5)]
class FilterConfiguratorListener
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return;
        }

        if ($this->em->getFilters()->has('owner_filter')) {
            $filter = $this->em->getFilters()->enable('owner_filter');
            $filter->setParameter('userId', $user->getId());
        }
    }
}
