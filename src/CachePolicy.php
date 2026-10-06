<?php

declare(strict_types=1);

namespace Marko\PageCache;

readonly class CachePolicy
{
    /**
     * @param array<string> $tags
     * @param array<string> $queryParams Query parameter names that form part of the cache key
     */
    public function __construct(
        public int $ttl,
        public array $tags,
        public array $queryParams = [],
    ) {}
}
