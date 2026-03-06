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
#[Route(path:"/new", name:"userNew")]

class NewAction extends AbstractAction {
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function call(Request $request): JsonResponse
    {
        $payload = ($request->getContent() ?? false) ? $request->toArray() : [];

        $username = $payload['username'] ?? null;
        if($username === null) return new JsonResponse(["status" => "error", "data" => ["message" => "Username not specified"]], status: 400);
        $email = $payload['email'] ?? null;
        if($email === null) return new JsonResponse(["status" => "error", "data" => ["message" => "E-mail not specified"]], status: 400);
        $password = $payload['password'] ?? null;
        if($password === null) return new JsonResponse(["status" => "error", "data" => ["message" => "Password not specified"]], status: 400);

        $currentUser = $this->getCurrentUser();
        // Option A: If we want to allow admins only to create users, we'd check roles here.
        // Option B: If we want to allow public registration, we don't return 401 if null.
        // However, the prompt says "All actions must call getCurrentUser... if null... return 401".
        // This effectively turns user/new into an authenticated-only endpoint.
        
        if ($currentUser === null) {
             return new JsonResponse(["status" => "error", "data" => ["message" => "User not authenticated"]], 401);
        }

        $user = $this->userService->newUser((string)$username, (string)$email, (string)$password);
        return new JsonResponse(["status" => "success", "data" => ["id" => $user->getId()]]);
    }
}