<?php

namespace Keyvanarasteh\LaravelBones\Console\Commands;

use Illuminate\Console\Command;
use Keyvanarasteh\LaravelBones\Services\Skeleton;

class ScanCommand extends Command
{
    protected $signature = 'bones:scan
                           {--only=* : Sections to inspect (routes, views, packages)}
                           {--json : Emit the raw skeleton as JSON}';

    protected $description = 'Inspect the application skeleton: routes, views and packages';

    public function handle(Skeleton $skeleton): int
    {
        $only = array_filter((array) $this->option('only')) ?: null;
        $data = $skeleton->inspect($only, fresh: true);

        if ($this->option('json')) {
            $this->output->writeln(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return Command::SUCCESS;
        }

        $this->newLine();
        $this->line('  <fg=blue;options=bold>Laravel Bones</> — <fg=gray>a skeleton of this application</>');
        $this->newLine();

        foreach ($data as $section) {
            $this->renderSection($section);
        }

        return Command::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $section
     */
    protected function renderSection(array $section): void
    {
        $summary = $section['data']['summary'] ?? [];
        $items = $section['data']['items'] ?? [];

        $this->line(sprintf(
            '  <fg=blue;options=bold>%s</>  <fg=gray>%s</>',
            $section['label'],
            $this->summaryLine($summary)
        ));

        $rows = $this->rowsFor($section['key'], $items);

        if ($rows !== []) {
            $this->table(array_keys($rows[0]), $rows);
        }

        $this->newLine();
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    protected function summaryLine(array $summary): string
    {
        return collect($summary)
            ->map(fn ($value, $key) => "{$key}: {$value}")
            ->implode('   ');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<int, string>>
     */
    protected function rowsFor(string $key, array $items): array
    {
        return match ($key) {
            'routes' => array_map(fn ($route) => [
                implode('|', $route['methods']),
                $route['uri'],
                (string) ($route['name'] ?? ''),
                $route['action']['full'] ?? 'Closure',
                $route['health']['ok'] ? '' : 'issue',
            ], $items),
            'views' => array_map(fn ($view) => [
                $view['name'],
                $view['kind'],
                (string) count($view['used_by']),
                $view['path'],
            ], $items),
            'packages' => array_map(fn ($package) => [
                $package['name'],
                (string) $package['version'],
                $package['direct'] ? 'yes' : '',
                $package['laravel'] ? 'yes' : '',
            ], $items),
            default => [],
        };
    }
}
