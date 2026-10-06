<?php

declare(strict_types=1);

namespace Marko\PageCache\Config;

use Marko\Config\ConfigRepositoryInterface;
use Marko\Config\Exceptions\ConfigNotFoundException;
use Marko\PageCache\Exceptions\PageCacheException;

readonly class PageCacheConfig
{
    public function __construct(
        private ConfigRepositoryInterface $configRepository,
    ) {}

    /**
     * @throws ConfigNotFoundException
     */
    public function driver(): string
    {
        return $this->configRepository->getString('page-cache.driver');
    }

    /**
     * @throws ConfigNotFoundException
     */
    public function path(): string
    {
        return $this->configRepository->getString('page-cache.path');
    }

    /**
     * Default page ttl in seconds. 0 means cached pages never expire and are only removed by a purge or clear.
     *
     * @throws ConfigNotFoundException|PageCacheException
     */
    public function defaultTtl(): int
    {
        $ttl = $this->configRepository->getInt('page-cache.default_ttl');

        if ($ttl < 0) {
            throw PageCacheException::negativeDefaultTtl($ttl);
        }

        return $ttl;
    }

    /**
     * @return array<int>
     *
     * @throws ConfigNotFoundException
     */
    public function cacheableStatusCodes(): array
    {
        return array_map(
            static fn (mixed $code): int => (int) $code,
            $this->configRepository->getArray('page-cache.cacheable_status_codes'),
        );
    }

    /**
     * @return array<string>
     *
     * @throws ConfigNotFoundException
     */
    public function cacheableMethods(): array
    {
        return array_map(
            static fn (mixed $method): string => strtoupper((string) $method),
            $this->configRepository->getArray('page-cache.cacheable_methods'),
        );
    }

    /**
     * Cookie names (fnmatch patterns) whose presence on a request bypasses the page cache entirely.
     *
     * When marko/session is installed, its configured session cookie name (session.cookie.name) is
     * always included, so a renamed session cookie can never be missed.
     *
     * @return array<string>
     *
     * @throws ConfigNotFoundException
     */
    public function bypassCookies(): array
    {
        $cookies = array_map(
            static fn (mixed $name): string => (string) $name,
            $this->configRepository->getArray('page-cache.bypass_cookies'),
        );

        if ($this->configRepository->has('session.cookie.name')) {
            $cookies[] = $this->configRepository->getString('session.cookie.name');
        }

        return array_values(array_unique(array_filter($cookies, static fn (string $name): bool => $name !== '')));
    }

    /**
     * Host names (fnmatch patterns) the page cache serves. An empty list allows every host.
     *
     * @return array<string>
     *
     * @throws ConfigNotFoundException
     */
    public function trustedHosts(): array
    {
        return array_map(
            static fn (mixed $host): string => strtolower((string) $host),
            $this->configRepository->getArray('page-cache.trusted_hosts'),
        );
    }

    /**
     * Middleware short class name patterns (fnmatch, case-insensitive) that mark a route as
     * authenticated. A #[Cacheable] route carrying one fails at boot.
     *
     * @return array<string>
     *
     * @throws ConfigNotFoundException
     */
    public function authMiddlewarePatterns(): array
    {
        return array_map(
            static fn (mixed $pattern): string => (string) $pattern,
            $this->configRepository->getArray('page-cache.auth_middleware_patterns'),
        );
    }
}
