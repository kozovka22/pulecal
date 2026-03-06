<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions\User;

use Pulecal\Service\Actions\AbstractAction;
use Pulecal\Service\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @property UserService $userService Holds operations on the user entity
 */
#[AsController()]
#[Route(path:"/detail", name:"userDetail")]

class DetailAction extends AbstractAction {
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function call(Request $request): JsonResponse
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            return new JsonResponse(["status" => "error", "data" => ["message" => "User not authenticated"]], 401);
        }

        $id = $request->query->get('id') ? (int)$request->query->get('id') : $currentUser->getId();
        $user = $this->userService->getInstanceById($id, $this->userService->getRepository());
        if (!$user) {
            return new JsonResponse(["status" => "error", "data" => ["message" => "User not found"]], 404);
        }

        if ($user->getId() !== $currentUser->getId() && !in_array('ROLE_ROOT', $currentUser->getRoles(), true)) {
            return new JsonResponse(["status" => "error", "data" => ["message" => "Access denied"]], 403);
        }

        return new JsonResponse(["status" => "success", "data" => $user->toArray()]);
    }
}