<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\Calendar;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Entity\Calendar;
use Pulecal\Service\Filter\CalendarFilter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pulecal\Service\Repository\UserRepository;
use Pulecal\Service\Service\CalendarService;
use Pulecal\Service\Service\UserService;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property CalendarService $calendarService Holds operations on the calendar entity
 * @property UserService $userService Holds operations on the user entity
*/
#[AsController()]
#[Route(path:"/list", name:"calendarList")]

class ListAction extends AbstractAction {

    private CalendarService $calendarService;
    private UserService $userService;
    public function __construct(
        CalendarService $calendarService, 
        UserService $userService
    ){
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

        $payload['userId'] = $currentUser->getId();
        $payload['ownerId'] = $currentUser->getId();

        $filter = new CalendarFilter(
            $this->userService->getRepository(),
            $payload
        );
        $calendars = $this->calendarService->listInstances($filter, $this->calendarService->getRepository());
        return new JsonResponse([
            "status" => "success",
            "data" => [
                "calendarIds" => array_values($calendars->map(
                    fn (Calendar $calendar): int => $calendar->getId()
                )->toArray())
            ]
        ], 200);
    }
}