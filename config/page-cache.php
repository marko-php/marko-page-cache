<?php

declare(strict_types=1);

use Marko\Config\Env;

return [
    'driver' => Env::string('PAGE_CACHE_DRIVER', 'file'),
    'path' => Env::string('PAGE_CACHE_PATH', 'storage/page-cache'),
    // Seconds a cached page stays fresh; 0 means it never expires (purge by tag, by URL or with page-cache:clear).
    'default_ttl' => Env::int('PAGE_CACHE_TTL', 3600, min: 0),
    // Maximum cached entries per URL path, counting every query variant, host, scheme and method. Further
    // variants of a path at the limit are served uncached. 0 disables the limit.
    'max_variants_per_path' => 1000,
    'cacheable_status_codes' => [200, 301],
    'cacheable_methods' => ['GET', 'HEAD'],
    // A request carrying any of these cookies (fnmatch patterns) is never served from or stored in the cache.
    // The session cookie configured in session.cookie.name is always added when marko/session is installed.
    'bypass_cookies' => ['marko_session', 'remember_*'],
    // Host names (fnmatch patterns, e.g. 'example.com', '*.example.com') the cache serves. Requests for any
    // other Host bypass the cache. Empty allows every host; each host still gets its own cache entries.
    'trusted_hosts' => [],
    // A #[Cacheable] route whose middleware short class name matches one of these (case-insensitive fnmatch)
    // fails at boot: cache hits are served before route middleware runs. Set to [] to disable the check.
    'auth_middleware_patterns' => ['*Auth*'],
];
