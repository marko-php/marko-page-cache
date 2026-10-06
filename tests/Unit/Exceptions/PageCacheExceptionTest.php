<?php

declare(strict_types=1);

use Marko\PageCache\Exceptions\PageCacheException;

it('constructs a base PageCacheException with message, context, and suggestion', function (): void {
    $exception = new PageCacheException(
        message: 'Test error',
        context: 'test context',
        suggestion: 'try this',
    );

    expect($exception->getMessage())->toBe('Test error')
        ->and($exception->getContext())->toBe('test context')
        ->and($exception->getSuggestion())->toBe('try this');
});

it('exposes context and suggestion via getter methods', function (): void {
    $exception = new PageCacheException(
        message: 'Test error',
        context: 'specific context',
        suggestion: 'specific suggestion',
    );

    expect($exception->getContext())->toBe('specific context')
        ->and($exception->getSuggestion())->toBe('specific suggestion');
});

it('creates negativeTtl exception with message, context and suggestion', function (): void {
    $exception = PageCacheException::negativeTtl(-5);

    expect($exception->getMessage())->toContain('-5')
        ->and($exception->getContext())->toContain('#[Cacheable]')
        ->and($exception->getSuggestion())->toContain('default_ttl');
});

it('creates negativeDefaultTtl exception with message, context and suggestion', function (): void {
    $exception = PageCacheException::negativeDefaultTtl(-10);

    expect($exception->getMessage())->toContain('-10')
        ->and($exception->getContext())->toContain('page-cache.default_ttl')
        ->and($exception->getSuggestion())->toContain('PAGE_CACHE_TTL');
});
