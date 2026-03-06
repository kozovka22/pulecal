<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions;

use Pulecal\Service\Entity\{User, Calendar, Event};
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAction {
    protected Security $security;

    #[Required]
    public function setSecurity(Security $security): void {
        $this->security = $security;
    }

    protected function getCurrentUser(): ?User {
        $user = $this->security->getUser();
        return $user instanceof User ? $user : null;
    }

    protected function json(array $data, int $status = 200, array $headers = []): JsonResponse {
        return new JsonResponse($data, $status, $headers);
    }

    public function __invoke(Request $request): JsonResponse {
        return $this->call($request);
    }

    public function idToClass(int $id, $service): User | Calendar | Event | bool {
        if (is_object($service) && method_exists($service, 'getInstanceById')) {
            $instance = $service->getInstanceById($id, $service->getRepository());
        } elseif (is_string($service) && isset($this->$service) && method_exists($this->$service, 'getInstanceById')) {
            $instance = $this->$service->getInstanceById($id, $this->$service->getRepository());
        } else {
            return false;
        }

        if($instance === null) return false;
        return $instance;
    }
    abstract public function call(Request $request): JsonResponse;

}