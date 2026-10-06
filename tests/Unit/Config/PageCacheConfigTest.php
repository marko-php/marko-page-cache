<?php

declare(strict_types=1);

use Marko\Config\Exceptions\ConfigNotFoundException;
use Marko\PageCache\Config\PageCacheConfig;
use Marko\PageCache\Exceptions\PageCacheException;
use Marko\Testing\Fake\FakeConfigRepository;

it('returns the configured driver name', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.driver' => 'redis',
    ]));

    expect($config->driver())->toBe('redis');
});

it('returns the configured storage path', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.path' => '/tmp/page-cache',
    ]));

    expect($config->path())->toBe('/tmp/page-cache');
});

it('returns the configured default ttl as int', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.default_ttl' => 7200,
    ]));

    expect($config->defaultTtl())->toBe(7200);
});

it('returns a default ttl of zero', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.default_ttl' => 0,
    ]));

    expect($config->defaultTtl())->toBe(0);
});

it('throws a PageCacheException when the default ttl is negative', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.default_ttl' => -60,
    ]));

    $config->defaultTtl();
})->throws(PageCacheException::class, 'Invalid page-cache.default_ttl -60');

it('returns the configured cacheable status codes as array of ints', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.cacheable_status_codes' => ['200', '301'],
    ]));

    expect($config->cacheableStatusCodes())->toBe([200, 301]);
});

it('returns the configured cacheable methods as array of strings', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.cacheable_methods' => ['get', 'head'],
    ]));

    expect($config->cacheableMethods())->toBe(['GET', 'HEAD']);
});

it('returns the configured bypass cookies', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.bypass_cookies' => ['marko_session', 'remember_*'],
    ]));

    expect($config->bypassCookies())->toBe(['marko_session', 'remember_*']);
});

it('adds the session cookie name from session.cookie.name to the bypass cookies', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.bypass_cookies' => ['remember_*'],
        'session.cookie.name' => 'shop_sid',
    ]));

    expect($config->bypassCookies())->toBe(['remember_*', 'shop_sid']);
});

it('returns the configured trusted hosts lowercased', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.trusted_hosts' => ['Example.com', '*.example.com'],
    ]));

    expect($config->trustedHosts())->toBe(['example.com', '*.example.com']);
});

it('returns the configured max variants per path', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.max_variants_per_path' => 250,
    ]));

    expect($config->maxVariantsPerPath())->toBe(250);
});

it('rejects a negative max variants per path with a PageCacheException', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.max_variants_per_path' => -1,
    ]));

    $config->maxVariantsPerPath();
})->throws(PageCacheException::class, 'Invalid page-cache.max_variants_per_path -1');

it('returns the configured auth middleware patterns', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([
        'page-cache.auth_middleware_patterns' => ['*Auth*'],
    ]));

    expect($config->authMiddlewarePatterns())->toBe(['*Auth*']);
});

it('propagates ConfigNotFoundException when a key is missing', function (): void {
    $config = new PageCacheConfig(new FakeConfigRepository([]));

    $config->driver();
})->throws(ConfigNotFoundException::class);
