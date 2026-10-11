<?php

namespace Keyvanarasteh\LaravelBones\Support;

class PackageCatalog
{
    /**
     * A curated shelf of packages worth knowing about, grouped by the job they
     * do. Bones cross-references these names against the installed list so the
     * dashboard can show what is already in play.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function defaults(): array
    {
        return [
            // Developer experience
            self::item('laravel/telescope', 'Debug and inspect requests, queries, jobs and more in a local dashboard.', 'https://laravel.com/docs/telescope', 'Developer experience', ['debug', 'local']),
            self::item('laravel/pint', 'An opinionated PHP code style fixer, zero configuration.', 'https://github.com/laravel/pint', 'Developer experience', ['style', 'ci']),
            self::item('larastan/larastan', 'PHPStan wrapper that understands Laravel magic.', 'https://github.com/larastan/larastan', 'Developer experience', ['static analysis', 'quality']),
            self::item('barryvdh/laravel-ide-helper', 'Generate IDE helper files for facades, models and containers.', 'https://github.com/barryvdh/laravel-ide-helper', 'Developer experience', ['ide']),
            self::item('laravel/pail', 'Tail your application logs from the command line.', 'https://github.com/laravel/pail', 'Developer experience', ['logs']),
            self::item('nunomaduro/collision', 'Beautiful error reporting for console and tests.', 'https://github.com/nunomaduro/collision', 'Developer experience', ['errors']),
            self::item('tightenco/duster', 'Automated linting, all the tools in one command.', 'https://github.com/tighten/duster', 'Developer experience', ['linting']),

            // Testing
            self::item('pestphp/pest', 'A delightful testing framework built on PHPUnit.', 'https://pestphp.com', 'Testing', ['testing', 'dx']),
            self::item('spatie/laravel-ray', 'Debug with Ray directly from your code.', 'https://github.com/spatie/laravel-ray', 'Testing', ['debug']),
            self::item('mikebronner/laravel-model-caching', 'Automatic caching for Eloquent models.', 'https://github.com/mikebronner/laravel-model-caching', 'Performance', ['cache', 'eloquent']),

            // Quality & permissions
            self::item('spatie/laravel-permission', 'Associate users with roles and permissions.', 'https://github.com/spatie/laravel-permission', 'Security', ['roles', 'auth']),
            self::item('spatie/laravel-activitylog', 'Log every model change for a full audit trail.', 'https://github.com/spatie/laravel-activitylog', 'Security', ['audit']),
            self::item('spatie/laravel-data', 'Powerful DTOs with validation and transformation.', 'https://github.com/spatie/laravel-data', 'Architecture', ['dto', 'validation']),
            self::item('spatie/laravel-sluggable', 'Generate slugs for any Eloquent model.', 'https://github.com/spatie/laravel-sluggable', 'Eloquent', ['slugs']),
            self::item('spatie/laravel-medialibrary', 'Associate files with models, at scale.', 'https://github.com/spatie/laravel-medialibrary', 'Media', ['uploads']),
            self::item('spatie/laravel-backup', 'Back up your app and database to anywhere.', 'https://github.com/spatie/laravel-backup', 'Operations', ['backup']),
            self::item('spatie/laravel-health', 'Run health checks for your application.', 'https://github.com/spatie/laravel-health', 'Operations', ['health']),
            self::item('spatie/laravel-schedule-monitor', 'Monitor your scheduled tasks.', 'https://github.com/spatie/laravel-schedule-monitor', 'Operations', ['scheduler']),

            // Performance & scaling
            self::item('laravel/horizon', 'Beautiful UI and configuration for Redis queues.', 'https://laravel.com/docs/horizon', 'Performance', ['queues']),
            self::item('laravel/octane', 'Supercharge with Swoole or RoadRunner.', 'https://laravel.com/docs/octane', 'Performance', ['octane']),
            self::item('laravel/pulse', 'Insights into your application performance and usage.', 'https://github.com/laravel/pulse', 'Performance', ['metrics']),
            self::item('laravel/scout', 'Full-text search for Eloquent models.', 'https://laravel.com/docs/scout', 'Performance', ['search']),
            self::item('laravel/socialite', 'OAuth authentication with popular providers.', 'https://laravel.com/docs/socialite', 'Security', ['oauth']),

            // API & structure
            self::item('dingo/api', 'A RESTful API package for Laravel.', 'https://github.com/dingo/api', 'API', ['api']),
            self::item('spatie/laravel-query-builder', 'Build Eloquent queries from API requests.', 'https://github.com/spatie/laravel-query-builder', 'API', ['api', 'filters']),
            self::item('spatie/laravel-fractal', 'A fractal wrapper for transformers.', 'https://github.com/spatie/laravel-fractal', 'API', ['api', 'transformers']),
            self::item('laravel/sanctum', 'Token-based API authentication.', 'https://laravel.com/docs/sanctum', 'API', ['auth', 'api']),
            self::item('nunomaduro/laravel-optimize-database', 'Safely optimise your database with the best indexes.', 'https://github.com/nunomaduro/laravel-optimize-database', 'Performance', ['database', 'indexes']),

            // Frontend
            self::item('inertiajs/inertia-laravel', 'Build single-page apps without an API.', 'https://inertiajs.com', 'Frontend', ['spa']),
            self::item('livewire/livewire', 'Dynamic interfaces, without leaving Laravel.', 'https://livewire.laravel.com', 'Frontend', ['reactive']),
            self::item('filament/filament', 'A complete admin panel toolkit.', 'https://filamentphp.com', 'Frontend', ['admin']),
        ];
    }

    /**
     * Merge the shipped catalogue with anything the app added in config.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        $custom = config('bones.explore', []);

        return array_merge(self::defaults(), is_array($custom) ? $custom : []);
    }

    /**
     * @param  array<int, string>  $tags
     * @return array<string, mixed>
     */
    protected static function item(string $name, string $description, string $url, string $category, array $tags): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'url' => $url,
            'category' => $category,
            'tags' => $tags,
        ];
    }
}
