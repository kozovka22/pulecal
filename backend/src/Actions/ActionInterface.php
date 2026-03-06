<?php
declare(strict_types=1);
namespace Pulecal\Service\Actions;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface ActionInterface {
    public function call(Request $request): JsonResponse;
}