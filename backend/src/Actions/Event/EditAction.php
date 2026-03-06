<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Event;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Service\CalendarService;
use Pulecal\Service\Service\EventService;
use Pulecal\Service\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property EventService $eventService Holds operations on the event entity
 * @property UserService $userService Holds operations on the user entity
 */

#[AsController()]
#[Route(path:"/edit", name:"eventEdit")]

class EditAction extends AbstractAction {
    private EventService $eventService;
    private UserService $userService;
    public function __construct(EventService $eventService, UserService $userService) {
        $this->eventService = $eventService;
        $this->userService = $userService;
    }
    public function call(Request $request): JsonResponse {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            return new JsonResponse(["status" => "error", "data" => ["message" => "User not authenticated"]], 401);
        }

        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];
        
        unset($payload['ownerId']);
        unset($payload['userId']);

        $event = $this->idToClass((int)$payload['eventId'], $this->eventService);
        if ($event === null) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Event not found"]
        ], 404);

        if ($this->eventService->verifyOwnership($event, $currentUser)) {
            $name = $payload['eventName'] ?? $payload['name'] ?? $event->getName();
            
            $start = $payload['start'] ?? $event->getStartTime();
            if (is_numeric($start)) {
                $start = (new \DateTime())->setTimestamp((int)$start);
            }

            $end = $payload['end'] ?? $event->getEndTime();
            if (is_numeric($end)) {
                $end = (new \DateTime())->setTimestamp((int)$end);
            }

            $repeats = $payload['repeats'] ?? $event->getRepeats();
            $repeatInterval = $payload['repeatInterval'] ?? $event->getRepeatInterval();
            $private = $payload['private'] ?? $event->isPrivate();
            $description = $payload['description'] ?? $event->getDescription();
            $adminDescription = $payload['adminDescription'] ?? $event->getAdminDescription();
            $status = $payload['status'] ?? $event->getStatus();

            $event = $this->eventService->editEvent($event, (string)$name, $start, $end, (bool)$repeats, $repeatInterval, (bool)$private, $description, $adminDescription);
            $event->setStatus($status);
            $this->eventService->save($event);

            return new JsonResponse([
                "status" => "success", 
                "data" => ["id" => $event->getId()]
            ], 200);
        }
        
        return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Access denied"]
        ], 403);
    }
}