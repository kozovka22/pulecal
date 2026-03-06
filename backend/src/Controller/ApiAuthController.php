<?php
declare(strict_types=1);

namespace Pulecal\Service\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Pulecal\Service\Entity\ApiKey;
use Pulecal\Service\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiAuthController {
    #[Route('/api/jwt', methods: 'POST')]
    public function jwtToken(Request $request, EntityManagerInterface $em, JWTTokenManagerInterface $jtm): JsonResponse {
        $apiKey = $request->headers->get('X-Api-Key');
        $key = $em->getRepository(ApiKey::class)->findOneBy(['key' => $apiKey]);
        if(null === $key) {
            return new JsonResponse(['success' => false, 'response' => 'Wrong API key!!!!!!'], 403);
        }
        $user = $key->getUser();
        return new JsonResponse([
            'success' => true,
            'token' => $jtm->create($user),
            'user' => $user->toArray()
        ]);
    }
}