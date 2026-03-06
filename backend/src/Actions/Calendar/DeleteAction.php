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
#[Route(path:"/delete", name:"calendarDelete")]

class DeleteAction extends AbstractAction {
    private UserService $userService;
    private CalendarService $calendarService;

    public function __construct(UserService $userService, CalendarService $calendarService)
    {
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
        
        $calendar = $this->idToClass((int)($payload['calendarId'] ?? 0), $this->calendarService);
        if (!$calendar) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Calendar not found"]
            ], 404);
        }

        if ($calendar->getOwner()->getId() === $currentUser->getId()) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Owner cannot remove themself from their own calendar"]
            ], 400);
        }

        $this->calendarService->removeUserFromCalendar($calendar, $currentUser);
        return new JsonResponse([
            "status" => "success", 
            "data" => ["id" => $currentUser->getId()]
        ], 200);
    }
}