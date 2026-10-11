<?php

namespace Keyvanarasteh\LaravelBones\Support;

use Keyvanarasteh\LaravelBones\Contracts\Probe;

class ProbeRegistry
{
    /** @var array<string, Probe> */
    protected array $probes = [];

    public function register(Probe $probe): void
    {
        $this->probes[$probe->key()] = $probe;
    }

    public function has(string $key): bool
    {
        return isset($this->probes[$key]);
    }

    public function get(string $key): ?Probe
    {
        return $this->probes[$key] ?? null;
    }

    /**
     * @return array<string, Probe>
     */
    public function all(): array
    {
        return $this->probes;
    }

    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->probes);
    }
}
