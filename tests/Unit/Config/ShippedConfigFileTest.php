<?php

declare(strict_types=1);

use Marko\Config\ConfigLoader;
use Marko\Config\Exceptions\ConfigException;

const PAGE_CACHE_CONFIG_FILE = __DIR__ . '/../../../config/page-cache.php';

beforeEach(function (): void {
    $this->originalTtl = $_ENV['PAGE_CACHE_TTL'] ?? null;
    unset($_ENV['PAGE_CACHE_TTL']);
});

afterEach(function (): void {
    if ($this->originalTtl === null) {
        unset($_ENV['PAGE_CACHE_TTL']);
    } else {
        $_ENV['PAGE_CACHE_TTL'] = $this->originalTtl;
    }
});

it('keeps the shipped default ttl of 3600 when PAGE_CACHE_TTL is unset', function (): void {
    $config = new ConfigLoader()->load(PAGE_CACHE_CONFIG_FILE);

    expect($config['default_ttl'])->toBe(3600);
});

it('reads PAGE_CACHE_TTL=0 as a ttl of 0 (never expires)', function (): void {
    $_ENV['PAGE_CACHE_TTL'] = '0';

    $config = new ConfigLoader()->load(PAGE_CACHE_CONFIG_FILE);

    expect($config['default_ttl'])->toBe(0);
});

it('fails config load when PAGE_CACHE_TTL is not an integer instead of producing never-expiring pages', function (
    string $value,
): void {
    $_ENV['PAGE_CACHE_TTL'] = $value;

    expect(fn (): array => new ConfigLoader()->load(PAGE_CACHE_CONFIG_FILE))
        ->toThrow(ConfigException::class, 'Environment variable "PAGE_CACHE_TTL" must be an integer');
})->with(['abc', '1h', '10s', '1.5']);

it('rejects a negative PAGE_CACHE_TTL at config load', function (): void {
    $_ENV['PAGE_CACHE_TTL'] = '-1';

    expect(fn (): array => new ConfigLoader()->load(PAGE_CACHE_CONFIG_FILE))
        ->toThrow(ConfigException::class, 'Environment variable "PAGE_CACHE_TTL" must be at least 0');
});
