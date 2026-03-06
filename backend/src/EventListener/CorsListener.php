<?php
namespace Pulecal\Service\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CorsListener implements EventSubscriberInterface {
    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // Allow CORS from localhost:5174 (pro testovací účely, smazat!)
        $origin = $request->headers->get('Origin');
        if ($origin === 'http://localhost:5174' || $origin === 'https://localhost:5174') {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->headers->set('Access-Control-Max-Age', '3600');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
    }
}
