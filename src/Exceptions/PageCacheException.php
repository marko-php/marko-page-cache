<?php

declare(strict_types=1);

namespace Marko\PageCache\Exceptions;

use Marko\Core\Exceptions\MarkoException;
use Marko\PageCache\Contracts\CacheTagProviderInterface;

class PageCacheException extends MarkoException
{
    public static function missingEntityBridge(string $offendingClass): self
    {
        return new self(
            message: "Class '$offendingClass' implements IdentityInterface but marko/page-cache-entity is not installed. The page cache will not be invalidated when this entity changes.",
            context: 'Detected during marko/page-cache boot validation',
            suggestion: 'Install the bridge package: composer require marko/page-cache-entity',
        );
    }

    public static function negativeTtl(int $ttl): self
    {
        return new self(
            message: "Invalid #[Cacheable] ttl $ttl: the ttl cannot be negative.",
            context: 'While constructing a #[Cacheable] attribute on a controller action.',
            suggestion: 'Use a positive number of seconds, or 0 to fall back to page-cache.default_ttl.',
        );
    }

    public static function negativeDefaultTtl(int $ttl): self
    {
        return new self(
            message: "Invalid page-cache.default_ttl $ttl: the default ttl cannot be negative.",
            context: 'While reading page-cache.default_ttl from config/page-cache.php.',
            suggestion: 'Set PAGE_CACHE_TTL (or page-cache.default_ttl) to a positive number of seconds, or to 0 for pages that never expire and are only removed by purge or page-cache:clear.',
        );
    }

    public static function invalidTagProvider(string $providerClass): self
    {
        return new self(
            message: "Class '$providerClass' does not implement CacheTagProviderInterface.",
            context: 'Resolved from the container as the tag provider for a #[Cacheable] attribute.',
            suggestion: 'Implement ' . CacheTagProviderInterface::class . " in '$providerClass'.",
        );
    }

    public static function cacheableRouteWithAuthMiddleware(
        string $controller,
        string $action,
        string $path,
        string $middleware,
    ): self {
        return new self(
            message: "Route '$path' ($controller::$action) is #[Cacheable] but uses authentication middleware '$middleware'. Cached pages are served by global middleware before route middleware runs, so this page would be served to unauthenticated visitors.",
            context: 'Detected during marko/page-cache boot validation',
            suggestion: 'Remove #[Cacheable] from authenticated routes. If the middleware does not authenticate, remove the pattern it matches from page-cache.auth_middleware_patterns.',
        );
    }

    public static function purgeUrlWithoutHost(string $url): self
    {
        return new self(
            message: "Cannot purge '$url': page cache entries are keyed by host, and the URL has no host.",
            context: 'While purging a page cache entry by URL.',
            suggestion: 'Pass an absolute URL (https://example.com/path), or list the site host names in page-cache.trusted_hosts so relative URLs are purged for each of them.',
        );
    }
}
