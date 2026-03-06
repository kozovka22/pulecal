<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Event;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Service\EventService;
use Pulecal\Service\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Pulecal\Service\Entity\Event;
use Pulecal\Service\Entity\User;

#[AsController()]
#[Route(path: "/purge", name: "eventPurge", methods: ["POST"])]
class PurgeAction extends AbstractAction
{
    private EventService $eventService;
    private UserService $userService;

    public function __construct(EventService $eventService, UserService $userService)
    {
        $this->eventService = $eventService;
        $this->userService = $userService;
    }

    public function call(Request $request): JsonResponse
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "User not authenticated"]
            ], 401);
        }

        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];
        
        if (!isset($payload['eventId'])) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Event ID is required"]
            ], 400);
        }

        $event = $this->idToClass((int)$payload['eventId'], $this->eventService);
        if (!$event instanceof Event) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Event not found"]
            ], 404);
        }

        if ($this->eventService->verifyOwnership($event, $currentUser)) {
            $eventId = $event->getId();
            $this->eventService->purgeEvent($event);
            return new JsonResponse([
                "status" => "success", 
                "data" => ["id" => $eventId]
            ], 200);
        }

        return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Access denied"]
        ], 403);
    }
}
