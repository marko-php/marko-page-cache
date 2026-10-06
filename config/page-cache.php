<?php

declare(strict_types=1);

use Marko\Config\Env;

return [
    'driver' => Env::string('PAGE_CACHE_DRIVER', 'file'),
    'path' => Env::string('PAGE_CACHE_PATH', 'storage/page-cache'),
    // Seconds a cached page stays fresh; 0 means it never expires (purge by tag, by URL or with page-cache:clear).
    'default_ttl' => Env::int('PAGE_CACHE_TTL', 3600, min: 0),
    'cacheable_status_codes' => [200, 301],
    'cacheable_methods' => ['GET', 'HEAD'],
];
