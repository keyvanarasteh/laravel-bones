<?php

namespace Keyvanarasteh\LaravelBones\Probes;

use Illuminate\Filesystem\Filesystem;
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

        usort($items, function (array $a, array $b) {
            return [$a['ecosystem'], $a['name']] <=> [$b['ecosystem'], $b['name']];
        });

        return [
            'summary' => $this->summarize($items),
            'items' => $items,
        ];
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
        ];
    }
}
