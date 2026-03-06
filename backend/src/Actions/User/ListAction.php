<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\User;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Entity\Event;
use Pulecal\Service\Entity\User;
use Pulecal\Service\Filter\UserFilter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pulecal\Service\Service\UserService;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property UserService $userService Holds operations on the user entity
*/
#[AsController()]
#[Route(path:"/list", name:"userList")]

class ListAction extends AbstractAction {
    private UserService $userService;
    public function __construct(
        UserService $userService, 
    ){
        $this->userService = $userService;
    }
    public function call(Request $request): JsonResponse {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            return new JsonResponse(["status" => "error", "data" => ["message" => "User not authenticated"]], 401);
        }

        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];

        if (in_array('ROLE_ROOT', $currentUser->getRoles(), true)) {
            $users = $this->userService->getRepository()->findAll();
            return $this->json([
                "status" => "success",
                "data" => array_map(
                    fn (User $user): array => $user->toArray(),
                    $users
                )
            ]);
        }

        $filter = new UserFilter(
            $this->userService->getRepository(),
            ['id' => $currentUser->getId()]
        );
        
        $users = $this->userService->listInstances($filter, $this->userService->getRepository());

        return $this->json([
            "status" => "success",
            "data" => $users->map(
                fn (User $user): array => $user->toArray()
            )->getValues()
        ]);
    }

    
}