<?php

declare(strict_types=1);

use Marko\Core\Module\ModuleRepositoryInterface;
use Marko\PageCache\Boot\CacheableRouteValidator;
use Marko\PageCache\Boot\IdentityBridgeValidator;
use Marko\PageCache\Middleware\PageCacheMiddleware;
use Marko\Routing\RouteCollection;

// Marko-specific configuration for this module.
// Name and version come from composer.json.

return [
    'boot' => function (
        IdentityBridgeValidator $validator,
        ModuleRepositoryInterface $modules,
        CacheableRouteValidator $routeValidator,
        RouteCollection $routes,
    ): void {
        $validator->validate($modules->all());
        $routeValidator->validate($routes);
    },
    'globalMiddleware' => [
        PageCacheMiddleware::class,
    ],
];
