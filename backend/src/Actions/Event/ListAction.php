<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Event;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Entity\Event;
use Pulecal\Service\Filter\EventFilter;
use Pulecal\Service\Repository\EventRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pulecal\Service\Service\EventService;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property EventService $eventService Holds operations on the event entity
*/
#[AsController()]
#[Route(path:"/list", name:"eventList")]

class ListAction extends AbstractAction {
    private EventService $eventService;
    public function __construct(
        EventService $eventService
    ){
        $this->eventService = $eventService;
    }
    public function call(Request $request): JsonResponse {
        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];

        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            return new JsonResponse(["status" => "error", "data" => ["message" => "User not authenticated"]], 401);
        }

        unset($payload['ownerId']);
        unset($payload['userId']);
        unset($payload['userIds']);

        $filter = new EventFilter(
            $this->eventService->getRepository(),
            $payload
        );
        
        $queryBuilder = $this->eventService->getRepository()->createQueryBuilder("e");
        $queryBuilder->select("e");
        
        $filter->toCriteria($queryBuilder);

        $queryBuilder->andWhere($queryBuilder->expr()->orX(
                "e.owner = :currentUser"
            ))
            ->setParameter("currentUser", $currentUser);

        $events = $queryBuilder->getQuery()->getResult();

        return new JsonResponse([
            "status" => "success",
            "data" => [
                "eventIds" => array_values(array_map(
                    fn (Event $event): int => $event->getId(),
                    $events
                ))
            ]
        ]);
    }

    
}