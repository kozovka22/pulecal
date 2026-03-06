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
#[Route(path:"/new", name:"eventNew")]

class NewAction extends AbstractAction {
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
        if (!$currentUser) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "User not authenticated"]
            ], 401);
        }

        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];

        unset($payload['ownerId']);

        $calendar = $this->idToClass((int)$payload['calendarId'], $this->calendarService);
        if (!$calendar) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Calendar not found"]
        ], status: 404);

        if ($calendar->getOwner() !== $currentUser && !$calendar->getUsers()->contains($currentUser)) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Access denied"]
            ], 403);
        }

        $name = $payload['eventName'] ?? $payload['name'] ?? null;
        if($name === null) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Event name not specified"]
        ], 400);

        $startTime = $payload['start'] ?? null;
        if($startTime === null) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "No start time specified"]
        ], 400);
        if (is_numeric($startTime)) {
            $startTime = (new \DateTime())->setTimestamp((int)$startTime);
        }

        $endTime = $payload['end'] ?? null;
        if($endTime === null) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "No end time specified"]
        ], 400);
        if (is_numeric($endTime)) {
            $endTime = (new \DateTime())->setTimestamp((int)$endTime);
        }

        $private = $payload['private'] ?? true;

        $description = $payload['description'] ?? null;

        $repeats = $payload['repeats'] ?? false;
        $repeatInterval = $payload['repeatInterval'] ?? null;
        $adminDescription = $payload['adminDescription'] ?? null;
        $status = $payload['status'] ?? 'waiting';
        $event = $this->eventService->newEvent($calendar, $currentUser, (string)$name, $startTime, $endTime, (bool)$private, $description, (bool)$repeats, $repeatInterval, $adminDescription, (string)$status);
        return new JsonResponse([
            "status" => "success", 
            "data" => ["id" => $event->getId()]
        ], 200);
    }
}