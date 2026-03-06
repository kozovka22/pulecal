<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Calendar;

use Pulecal\Service\Actions\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Pulecal\Service\Service\CalendarService;

/**
 * @property CalendarService $calendarService Holds operations on the calendar entity
*/
#[AsController()]
#[Route(path:"/detail", name:"calendarDetail")]

class DetailAction extends AbstractAction {
 
    private CalendarService $calendarService;
    public function __construct(CalendarService $calendarService)
    {
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

        $id = (int)$request->query->get(key: 'id');
        $calendar = $this->idToClass($id, $this->calendarService);
        if(!$calendar) return new JsonResponse([
            "status" => "error", 
            "data" => ["message" => "Calendar not found"]
        ], 404);

        if ($calendar->getOwner() !== $currentUser && !$calendar->getUsers()->contains($currentUser)) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Access denied"]
            ], 403);
        }

        return new JsonResponse([
            "status" => "success", 
            "data" => $calendar->toArray()
        ], 200);
    }
}