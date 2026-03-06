<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Calendar;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Service\{CalendarService, UserService};
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property CalendarService $calendarService Holds operations on the calendar entity
 * @property UserService $userService Holds operations on the user entity
 */
#[AsController()]
#[Route('/new', 'calendarNew')]
class NewAction extends AbstractAction {
    private CalendarService $calendarService;
    private UserService $userService;

    public function __construct(CalendarService $calendarService, UserService $userService)
    {
        $this->calendarService = $calendarService;
        $this->userService = $userService;
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
        unset($payload['userId']);

        $calName = $payload['calendarName'] ?? $payload['name'] ?? null;
        if($calName === null) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Calendar name not specified"]
        ], 400);
        
        $description = $payload['description'] ?? null;
        $adminDescription = $payload['adminDescription'] ?? null;
        
        $calendar = $this->calendarService->newCalendar($calName, $currentUser, description: $description, adminDescription: $adminDescription);
        return new JsonResponse([
            "status" => "success", 
            "data" => ["id" => $calendar->getId()]
        ], 200);
    }
}