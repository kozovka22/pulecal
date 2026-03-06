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
 * @property CalendarService $calendarService Holds operations on the calendar entity
 */
#[AsController()]
#[Route(path:"/delete", name:"eventDelete")]

class DeleteAction extends AbstractAction {
    private EventService $eventService;
    private UserService $userService;
    private CalendarService $calendarService;
    public function __construct(EventService $eventService, UserService $userService, CalendarService $calendarService) {
        $this->eventService = $eventService;
        $this->userService = $userService;
        $this->calendarService = $calendarService;
    }
    public function call(Request $request): JsonResponse {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "User not authenticated"]
            ], 401);
        }

        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];

        $event = $this->idToClass((int)$payload['eventId'], $this->eventService);
        if ($event === null) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Event not found"]
            ], 404);
        }

        $calendar = $this->idToClass((int)$payload['calendarId'], $this->calendarService);
        if ($calendar === null) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Calendar not found"]
            ], 404);
        }

        if($this->eventService->verifyOwnership($event, $currentUser)) {
            $this->eventService->deleteEvent($event, $calendar);
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