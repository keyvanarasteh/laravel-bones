<?php

namespace Keyvanarasteh\LaravelBones\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Keyvanarasteh\LaravelBones\Support\ProbeRegistry;

class Skeleton
{
    public function __construct(
        protected ProbeRegistry $registry,
        protected CacheRepository $cache,
    ) {
    }

    /**
     * Inspect every registered probe and return the full skeleton.
     *
     * @param  array<int, string>|null  $only
     * @return array<string, array<string, mixed>>
     */
    public function inspect(?array $only = null, bool $fresh = false): array
    {
        $keys = $only ?: $this->registry->keys();

        if ($fresh || ! config('bones.cache.enabled', true)) {
            return $this->build($keys);
        }

        return $this->cache->remember(
            config('bones.cache.key', 'bones.skeleton'),
            config('bones.cache.ttl', 600),
            fn () => $this->build($keys),
        );
    }

    /**
     * Counts pulled from every section, ready for the overview tiles.
     *
     * @return array<string, int>
     */
    public function totals(?array $only = null, bool $fresh = false): array
    {
        $skeleton = $this->inspect($only, $fresh);

        return collect($skeleton)
            ->mapWithKeys(fn (array $section) => [$section['key'] => count($section['data']['items'] ?? [])])
            ->all();
    }

    public function forget(): void
    {
        $this->cache->forget(config('bones.cache.key', 'bones.skeleton'));
    }

    /**
     * @param  array<int, string>  $keys
     * @return array<string, array<string, mixed>>
     */
    protected function build(array $keys): array
    {
        $sections = [];

        foreach ($keys as $key) {
            $probe = $this->registry->get($key);

            if ($probe === null) {
                continue;
            }

            $sections[$key] = [
                'key' => $probe->key(),
                'label' => $probe->label(),
                'description' => $probe->description(),
                'icon' => $probe->icon(),
                'data' => $probe->scan(),
            ];
        }

        return $sections;
    }
}
