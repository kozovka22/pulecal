<?php
namespace Pulecal\Service\Security;

use Pulecal\Service\Entity\ApiKey;
use Pulecal\Service\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): ?bool
    {
                return $request->headers->has('X-API-Key');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $apiKey = $request->headers->get('X-API-Key');
        
        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API key provided');
        }

        $apiKeyEntity = $this->entityManager->getRepository(ApiKey::class)
            ->findOneBy(['key' => $apiKey]);

        if (!$apiKeyEntity) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        if ($apiKeyEntity->getExpiresAt() < new \DateTime()) {
            throw new CustomUserMessageAuthenticationException('API key has expired');
        }

        $user = $apiKeyEntity->getUser();

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier())
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; 
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessageKey()], Response::HTTP_UNAUTHORIZED);
    }
}
