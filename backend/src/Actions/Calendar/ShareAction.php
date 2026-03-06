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
#[Route(path:"/share", name:"calendarShare")]

class ShareAction extends AbstractAction {
    private CalendarService $calendarService;
    private UserService $userService;
    public function __construct(CalendarService $calendarService, UserService $userService) {
        $this->calendarService = $calendarService;
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

        $user = $this->idToClass((int)$payload['userId'], $this->userService);
        if ($user === null) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "User not found"]
        ], 404);
        
        $calendar = $this->idToClass((int)$payload['calendarId'], $this->calendarService);
        if ($calendar === null) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Calendar not found"]
        ], 404);

        if($this->calendarService->verifyOwnership($calendar, $currentUser)){
            $calendar = $this->calendarService->shareWithUser($calendar, $user);
            return new JsonResponse([
                "status" => "success", 
                "data" => ["id" => $calendar->getId()]
            ]);
        }
        
        return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Access denied"]
        ], 403);
    }
}