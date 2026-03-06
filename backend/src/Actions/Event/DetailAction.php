<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Event;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Service\EventService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property EventService $eventService Holds operations on the event entity
 */
#[AsController()]
#[Route(path:"/detail", name:"eventDetail")]

class DetailAction extends AbstractAction {
    private EventService $eventService;
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
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

        $id = (int)$request->query->get('eventId');
        $event = $this->eventService->getInstanceById($id, $this->eventService->getRepository());
        
        if (!$event) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Event not found"]
            ], 404);
        }

        $isOwner = $event->getOwner() === $currentUser;

        if (!$isOwner) {
            return new JsonResponse([
                "status" => "error", 
                "data" => ["message" => "Access denied"]
            ], 403);
        }

        return new JsonResponse([
            "status" => "success", 
            "data" => $event->toArray()
        ], 200);
    }
}
