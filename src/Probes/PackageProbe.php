<?php

namespace Keyvanarasteh\LaravelBones\Probes;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Http;
use Keyvanarasteh\LaravelBones\Contracts\Probe;

class PackageProbe implements Probe
{
    public function __construct(protected Filesystem $files)
    {
    }

    public function key(): string
    {
        return 'packages';
    }

    public function label(): string
    {
        return 'Packages';
    }

    public function description(): string
    {
        return 'Composer and npm dependencies holding the app up.';
    }

    public function icon(): string
    {
        return 'package';
    }

    public function scan(): array
    {
        $items = array_merge($this->composerPackages(), $this->npmPackages());
        $items = $this->enrich($items);

        usort($items, function (array $a, array $b) {
            return [$a['ecosystem'], $a['name']] <=> [$b['ecosystem'], $b['name']];
        });

        return [
            'summary' => $this->summarize($items),
            'items' => $items,
        ];
    }

    /**
     * Best-effort supply-chain signals (newer releases and security
     * advisories) fetched from Packagist and cached with the skeleton.
     * Both are optional and never allowed to break the scan.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    protected function enrich(array $items): array
    {
        $index = [];
        foreach ($items as $position => $item) {
            $index[$item['name']] = $position;
        }

        if ($items !== [] && config('bones.packages.check_security', true)) {
            foreach ($this->advisories(array_keys($index)) as $name => $list) {
                if (isset($index[$name])) {
                    $items[$index[$name]]['advisories'] = $list;
                }
            }
        }

        if ($items !== [] && config('bones.packages.check_updates', true)) {
            $direct = array_values(array_filter(
                $items,
                fn (array $package) => $package['ecosystem'] === 'composer' && $package['direct']
            ));

            foreach ($this->latestVersions($direct) as $name => $latest) {
                if (isset($index[$name])) {
                    $items[$index[$name]]['latest'] = $latest;
                }
            }
        }

        foreach ($items as &$item) {
            $item['advisories'] = $item['advisories'] ?? [];
            $item['latest'] = $item['latest'] ?? null;
            $item['severity'] = $this->severity($item['advisories']);
            $item['outdated'] = ! empty($item['latest']) && ! empty($item['version'])
                && $this->isNewer($item['latest'], $item['version']);
        }
        unset($item);

        return $items;
    }

    /**
     * @param  array<int, string>  $names
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function advisories(array $names): array
    {
        $names = array_values(array_filter($names, fn (string $name) => str_contains($name, '/')));
        $result = [];

        foreach (array_chunk($names, 50) as $chunk) {
            try {
                $query = implode('&', array_map(fn (string $name) => 'packages[]='.rawurlencode($name), $chunk));
                $response = Http::timeout((int) config('bones.packages.timeout', 6))
                    ->acceptJson()
                    ->get('https://packagist.org/api/security-advisories/?'.$query);

                if (! $response->successful()) {
                    continue;
                }

                foreach (($response->json('advisories') ?? []) as $name => $list) {
                    $result[$name] = array_map(fn (array $advisory) => [
                        'id' => $advisory['advisoryId'] ?? null,
                        'title' => $advisory['title'] ?? null,
                        'cve' => $advisory['cve'] ?? null,
                        'link' => $advisory['link'] ?? null,
                        'severity' => strtolower((string) ($advisory['severity'] ?? '')),
                    ], $list);
                }
            } catch (\Throwable) {
                // Network is best-effort; a failure must never break the scan.
            }
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, string>
     */
    protected function latestVersions(array $items): array
    {
        $names = array_values(array_unique(array_map(fn (array $package) => $package['name'], $items)));
        $names = array_values(array_filter($names, fn (string $name) => str_contains($name, '/')));

        if ($names === []) {
            return [];
        }

        try {
            $responses = Http::pool(function ($pool) use ($names) {
                $pending = [];

                foreach ($names as $name) {
                    $pending[] = $pool->as($name)
                        ->timeout((int) config('bones.packages.timeout', 6))
                        ->acceptJson()
                        ->get('https://repo.packagist.org/p2/'.$name.'.json');
                }

                return $pending;
            });
        } catch (\Throwable) {
            return [];
        }

        $result = [];

        foreach ($names as $name) {
            $response = $responses[$name] ?? null;

            if ($response === null || ! $response->successful()) {
                continue;
            }

            foreach (($response->json('packages.'.$name) ?? []) as $version) {
                $number = ltrim((string) ($version['version'] ?? ''), 'v');

                if ($number === '' || str_contains($number, '-') || str_contains($number, 'dev')) {
                    continue;
                }

                $result[$name] = $number;
                break;
            }
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $advisories
     */
    protected function severity(array $advisories): ?string
    {
        $rank = ['low' => 1, 'moderate' => 2, 'high' => 3, 'critical' => 4];
        $max = null;

        foreach ($advisories as $advisory) {
            $severity = $advisory['severity'] ?? '';

            if (isset($rank[$severity]) && ($max === null || $rank[$severity] > $rank[$max])) {
                $max = $severity;
            }
        }

        return $max;
    }

    protected function isNewer(string $latest, string $current): bool
    {
        $latest = ltrim($latest, 'v');
        $current = ltrim($current, 'v');

        if (! preg_match('/^\d/', $latest) || ! preg_match('/^\d/', $current)) {
            return false;
        }

        return version_compare($latest, $current, '>');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function composerPackages(): array
    {
        $manifest = $this->json($this->composerManifestPath());
        $require = array_keys($manifest['require'] ?? []);
        $requireDev = array_keys($manifest['require-dev'] ?? []);

        $installed = $this->installedComposerPackages();

        $items = [];

        foreach ($installed as $package) {
            $name = $package['name'] ?? null;

            if ($name === null) {
                continue;
            }

            $extra = $package['extra'] ?? [];
            $providers = $extra['laravel']['providers'] ?? [];

            $items[] = [
                'name' => $name,
                'version' => ltrim((string) ($package['version'] ?? ''), 'v'),
                'description' => $package['description'] ?? null,
                'homepage' => $package['homepage'] ?? null,
                'license' => $this->license($package['license'] ?? []),
                'type' => $package['type'] ?? 'library',
                'abandoned' => (bool) ($package['abandoned'] ?? false),
                'ecosystem' => 'composer',
                'direct' => in_array($name, $require, true),
                'dev' => in_array($name, $requireDev, true),
                'laravel' => $providers !== [] || ($extra['laravel'] ?? []) !== [],
                'providers' => count($providers),
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function installedComposerPackages(): array
    {
        $path = $this->installedPath();

        if (! $this->files->exists($path)) {
            return [];
        }

        $data = $this->json($path);

        if (isset($data['packages']) && is_array($data['packages'])) {
            return $data['packages'];
        }

        return array_is_list($data) ? $data : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function npmPackages(): array
    {
        if (! config('bones.packages.include_npm', true)) {
            return [];
        }

        $path = $this->npmManifestPath();

        if (! $this->files->exists($path)) {
            return [];
        }

        $manifest = $this->json($path);
        $items = [];

        foreach (['dependencies' => false, 'devDependencies' => true] as $key => $isDev) {
            foreach ($manifest[$key] ?? [] as $name => $constraint) {
                $items[] = [
                    'name' => $name,
                    'version' => ltrim((string) $constraint, '^~>=< '),
                    'description' => null,
                    'homepage' => 'https://www.npmjs.com/package/'.$name,
                    'license' => null,
                    'type' => 'npm',
                    'abandoned' => false,
                    'ecosystem' => 'npm',
                    'direct' => true,
                    'dev' => $isDev,
                    'laravel' => false,
                    'providers' => 0,
                ];
            }
        }

        return $items;
    }

    protected function composerManifestPath(): string
    {
        return base_path('composer.json');
    }

    protected function installedPath(): string
    {
        return base_path('vendor/composer/installed.json');
    }

    protected function npmManifestPath(): string
    {
        return base_path('package.json');
    }

    /**
     * @return array<string, mixed>
     */
    protected function json(string $path): array
    {
        if (! $this->files->exists($path)) {
            return [];
        }

        $decoded = json_decode($this->files->get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function license(mixed $license): ?string
    {
        if (is_array($license)) {
            return $license === [] ? null : implode(', ', $license);
        }

        return is_string($license) ? $license : null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, int>
     */
    protected function summarize(array $items): array
    {
        $collection = collect($items);

        return [
            'total' => $collection->count(),
            'composer' => $collection->where('ecosystem', 'composer')->count(),
            'npm' => $collection->where('ecosystem', 'npm')->count(),
            'direct' => $collection->where('direct', true)->count(),
            'dev' => $collection->where('dev', true)->count(),
            'laravel' => $collection->where('laravel', true)->count(),
            'abandoned' => $collection->where('abandoned', true)->count(),
            'outdated' => $collection->where('outdated', true)->count(),
            'vulnerable' => $collection->filter(fn (array $package) => ! empty($package['advisories']))->count(),
            'advisories' => $collection->sum(fn (array $package) => count($package['advisories'] ?? [])),
        ];
    }
}
