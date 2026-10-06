<?php

declare(strict_types=1);

use Marko\PageCache\CachePolicy;

it('builds a cache policy from ttl and tags', function (): void {
    $policy = new CachePolicy(ttl: 3600, tags: ['product', 'listing']);

    expect($policy->ttl)->toBe(3600)
        ->and($policy->tags)->toBe(['product', 'listing']);
});

it('accepts an empty tags array on a cache policy', function (): void {
    $policy = new CachePolicy(ttl: 60, tags: []);

    expect($policy->tags)->toBeEmpty();
});

it('defaults to no query parameters in the key and stores an allowlist when given', function (): void {
    expect(new CachePolicy(ttl: 60, tags: [])->queryParams)->toBe([])
        ->and(new CachePolicy(ttl: 60, tags: [], queryParams: ['page'])->queryParams)->toBe(['page']);
});
