<?php

namespace Keyvanarasteh\LaravelBones\Probes;

use Illuminate\Filesystem\Filesystem;
use Keyvanarasteh\LaravelBones\Contracts\Probe;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class ViewProbe implements Probe
{
    /** @var array<int, array{prefix: ?string, dir: string}> */
    protected array $roots = [];

    public function __construct(protected Filesystem $files)
    {
    }

    public function key(): string
    {
        return 'views';
    }

    public function label(): string
    {
        return 'Views';
    }

    public function description(): string
    {
        return 'Blade view nodes and how they knit together.';
    }

    public function icon(): string
    {
        return 'layout';
    }

    public function scan(): array
    {
        $views = $this->collectViews();
        $views = $this->resolveReferences($views);

        return [
            'summary' => $this->summarize($views),
            'items' => array_values($views),
            'graph' => $this->graph($views),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function collectViews(): array
    {
        $views = [];

        foreach ($this->roots() as $root) {
            foreach ($this->bladeFiles($root['dir']) as $file) {
                $relative = $this->relative($file->getPathname(), $root['dir']);
                $name = $this->viewName($relative, $root['prefix']);
                $content = $this->files->get($file->getPathname());

                $views[$name] = [
                    'name' => $name,
                    'file' => $file->getPathname(),
                    'path' => $relative,
                    'package' => $root['prefix'],
                    'size' => $file->getSize(),
                    'lines' => substr_count($content, "\n") + 1,
                    'kind' => $this->kind($relative, $content),
                    'extends' => $this->match($content, '/@extends\s*\(\s*[\'"]([^\'"]+)[\'"]/'),
                    'includes' => array_values(array_unique(array_merge(
                        $this->match($content, '/@include(?:If|When|First|Unless)?\s*\(\s*[\'"]([^\'"]+)[\'"]/'),
                        $this->match($content, '/@each\s*\(\s*[\'"]([^\'"]+)[\'"]/'),
                        $this->match($content, '/@component\s*\(\s*[\'"]([^\'"]+)[\'"]/'),
                    ))),
                    'components' => array_values(array_unique($this->match($content, '/<x-([a-zA-Z0-9\-\.:]+)/'))),
                    'used_by' => [],
                ];
            }
        }

        ksort($views);

        return $views;
    }

    /**
     * Inverse the reference lists so every view knows who reaches for it.
     *
     * @param  array<string, array<string, mixed>>  $views
     * @return array<string, array<string, mixed>>
     */
    protected function resolveReferences(array $views): array
    {
        foreach ($views as $name => $view) {
            foreach (array_merge($view['extends'], $view['includes']) as $target) {
                if (isset($views[$target]) && $target !== $name) {
                    $views[$target]['used_by'][] = $name;
                }
            }
        }

        foreach ($views as $name => $view) {
            $views[$name]['used_by'] = array_values(array_unique($view['used_by']));
        }

        return $views;
    }

    /**
     * @param  array<string, array<string, mixed>>  $views
     * @return array{nodes: array<int, mixed>, edges: array<int, mixed>}
     */
    protected function graph(array $views): array
    {
        $nodes = [];
        $edges = [];

        foreach ($views as $name => $view) {
            $nodes[] = [
                'id' => $name,
                'label' => $name,
                'kind' => $view['kind'],
                'package' => $view['package'],
            ];

            foreach (array_merge($view['extends'], $view['includes']) as $target) {
                if (isset($views[$target])) {
                    $edges[] = ['from' => $name, 'to' => $target];
                }
            }
        }

        return ['nodes' => $nodes, 'edges' => $edges];
    }

    /**
     * @param  array<string, array<string, mixed>>  $views
     * @return array<string, int>
     */
    protected function summarize(array $views): array
    {
        $collection = collect($views);

        return [
            'total' => $collection->count(),
            'layouts' => $collection->where('kind', 'layout')->count(),
            'components' => $collection->where('kind', 'component')->count(),
            'packages' => $collection->pluck('package')->filter()->unique()->count(),
            'orphans' => $collection->filter(
                fn ($view) => $view['kind'] === 'view' && $view['used_by'] === []
            )->count(),
        ];
    }

    /**
     * @return array<int, array{prefix: ?string, dir: string}>
     */
    protected function roots(): array
    {
        if ($this->roots !== []) {
            return $this->roots;
        }

        $roots = [];

        $appViews = resource_path('views');
        if ($this->files->isDirectory($appViews)) {
            $roots[] = ['prefix' => null, 'dir' => $appViews];
        }

        foreach (config('bones.views.paths', []) as $path) {
            if ($this->files->isDirectory($path)) {
                $roots[] = ['prefix' => null, 'dir' => $path];
            }
        }

        if (config('bones.views.include_namespaces', true)) {
            foreach ($this->namespacedHints() as $prefix => $paths) {
                foreach ($paths as $path) {
                    if ($this->files->isDirectory($path)) {
                        $roots[] = ['prefix' => $prefix, 'dir' => $path];
                    }
                }
            }
        }

        return $this->roots = $roots;
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function namespacedHints(): array
    {
        try {
            $finder = app('view')->getFinder();
        } catch (\Throwable) {
            return [];
        }

        if (! method_exists($finder, 'getHints')) {
            return [];
        }

        return $finder->getHints();
    }

    /**
     * @return iterable<SplFileInfo>
     */
    protected function bladeFiles(string $dir): iterable
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                yield $file;
            }
        }
    }

    protected function relative(string $path, string $root): string
    {
        $root = rtrim($root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return str_starts_with($path, $root) ? substr($path, strlen($root)) : $path;
    }

    protected function viewName(string $relative, ?string $prefix): string
    {
        $name = str_replace(['\\', '/'], '.', preg_replace('/\.blade\.php$/', '', $relative));

        return $prefix ? $prefix.'::'.$name : $name;
    }

    protected function kind(string $relative, string $content): string
    {
        if (str_contains($relative, 'components'.DIRECTORY_SEPARATOR) || preg_match('/@props\s*\(/', $content)) {
            return 'component';
        }

        if (! preg_match('/@extends\s*\(/', $content)
            && (preg_match('/@yield\s*\(/', $content) || preg_match('/@show\b/', $content))) {
            return 'layout';
        }

        return 'view';
    }

    /**
     * @return array<int, string>
     */
    protected function match(string $content, string $pattern): array
    {
        preg_match_all($pattern, $content, $matches);

        return $matches[1] ?? [];
    }
}
