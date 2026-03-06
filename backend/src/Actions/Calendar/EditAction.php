<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Calendar;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Service\CalendarService;
use Pulecal\Service\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property CalendarService $calendarService Holds operations on the calendar entity
 * @property UserService $userService Holds operations on the user entity
 */
#[AsController()]
#[Route(path:"/edit", name:"calendarEdit")]

class EditAction extends AbstractAction {
    public CalendarService $calendarService;
    public UserService $userService;
    public function __construct(CalendarService $calendarService, UserService $userService) {
        $this->calendarService = $calendarService;
        $this->userService = $userService;
    }
    public function call(Request $request): JsonResponse {
        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];
        
        unset($payload['ownerId']);
        unset($payload['userId']);

        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "User not authenticated"]
            ], 401);
        }

        $calendar = $this->idToClass((int)$payload['calendarId'], $this->calendarService);
        if(!$calendar) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Calendar not found"]
        ], 404);

        $name = $payload['calendarName'] ?? $payload['name'] ?? $calendar->getName();
        $private = $payload['private'] ?? $calendar->isPrivate();
        $description = $payload['description'] ?? $calendar->getDescription();
        $adminDescription = $payload['adminDescription'] ?? $calendar->getAdminDescription();
        
        if($this->calendarService->verifyOwnership($calendar, $currentUser)) {
            $calendar = $this->calendarService->editCalendar($calendar, (string)$name, (bool)$private, $description, $adminDescription);
            return new JsonResponse(["status" => "success", "data" => ["id" => $calendar->getId()]]);
        }
        
        return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Access denied"]
        ], 403);
    }
}