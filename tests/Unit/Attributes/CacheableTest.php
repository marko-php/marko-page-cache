<?php

declare(strict_types=1);

use Marko\PageCache\Attributes\Cacheable;
use Marko\PageCache\Exceptions\PageCacheException;

class CacheableFixture
{
    #[Cacheable(ttl: 3600, tags: ['products'])]
    public function index(): void {}
}

it('stores ttl and tags as public properties', function (): void {
    $cacheable = new Cacheable(ttl: 300, tags: ['products', 'catalog']);

    expect($cacheable->ttl)->toBe(300)
        ->and($cacheable->tags)->toBe(['products', 'catalog']);
});

it('accepts an empty tags array by default', function (): void {
    $cacheable = new Cacheable(ttl: 60);

    expect($cacheable->tags)->toBeEmpty();
});

it('can be discovered via reflection on a method that declares it', function (): void {
    $method = new ReflectionMethod(CacheableFixture::class, 'index');
    $attributes = $method->getAttributes(Cacheable::class);

    expect($attributes)->toHaveCount(1);

    $instance = $attributes[0]->newInstance();

    expect($instance->ttl)->toBe(3600)
        ->and($instance->tags)->toBe(['products']);
});

it('accepts a ttl of zero', function (): void {
    $cacheable = new Cacheable(ttl: 0);

    expect($cacheable->ttl)->toBe(0);
});

it('rejects a negative ttl with a PageCacheException', function (): void {
    new Cacheable(ttl: -1);
})->throws(PageCacheException::class, 'Invalid #[Cacheable] ttl -1');

it('defaults to an empty query parameter allowlist', function (): void {
    expect(new Cacheable(ttl: 60)->query)->toBe([]);
});

it('stores the query parameter allowlist', function (): void {
    expect(new Cacheable(ttl: 60, query: ['page', 'sort'])->query)->toBe(['page', 'sort']);
});

it('rejects an empty query parameter name with a PageCacheException', function (): void {
    new Cacheable(ttl: 60, query: ['page', '']);
})->throws(PageCacheException::class, "Invalid #[Cacheable] query parameter ''");

it('rejects a non-string query parameter name with a PageCacheException', function (): void {
    new Cacheable(ttl: 60, query: [1]);
})->throws(PageCacheException::class, 'Invalid #[Cacheable] query parameter int');
