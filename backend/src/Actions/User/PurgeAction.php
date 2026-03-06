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
#[Route(path:"/purge", name:"userPurge")]

class PurgeAction extends AbstractAction {
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

        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];

        $userId = $payload['userId'] ?? $currentUser->getId();
        
        $user = $this->userService->getInstanceById((int)$userId, $this->userService->getRepository());
        if($user === null) return new JsonResponse(["status" => "error", "data" => ["message" => "User not found"]], status: 404);

        if ($user->getId() !== $currentUser->getId() && !in_array('ROLE_ROOT', $currentUser->getRoles(), true)) {
            return new JsonResponse(["status" => "error", "data" => ["message" => "Access denied"]], 403);
        }

        $this->userService->deleteUser($user);
        return new JsonResponse(["status" => "success", "data" => ["id" => $user->getId()]], 200);
    }
}