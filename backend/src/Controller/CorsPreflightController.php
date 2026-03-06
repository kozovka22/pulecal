<?php
namespace Pulecal\Service\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Claude Haiku 4.5, povoleni vyuziti javascriptu z vite localhostu
class CorsPreflightController extends AbstractController {
    #[Route('/{path}', name: 'cors_options', methods: ['OPTIONS'], requirements: ['path' => '.+'])]
    public function handleOptions(string $path): Response {
        $response = new Response();
        $response->setStatusCode(200);
        
        return $response;
    }
}
