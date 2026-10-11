<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Bones is a development tool. It stays off in production unless you
    | explicitly switch it on through the environment variable below.
    |
    */

    'enabled' => env('BONES_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix & Middleware
    |--------------------------------------------------------------------------
    |
    | Where the dashboard lives and what guards it. Add "auth" or any custom
    | middleware when you expose the dashboard outside of local development.
    |
    */

    'route_prefix' => env('BONES_ROUTE_PREFIX', 'bones'),

    'middleware' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('BONES_MIDDLEWARE', 'web'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Probes
    |--------------------------------------------------------------------------
    |
    | Each probe inspects one facet of the application. Disable a probe to
    | drop it from the dashboard, or append your own class that implements
    | Keyvanarasteh\LaravelBones\Contracts\Probe.
    |
    */

    'probes' => [
        \Keyvanarasteh\LaravelBones\Probes\RouteProbe::class => true,
        \Keyvanarasteh\LaravelBones\Probes\ViewProbe::class => true,
        \Keyvanarasteh\LaravelBones\Probes\PackageProbe::class => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Scanning routes, views and packages is cheap but not free. The result is
    | cached for the given number of seconds; the dashboard offers a refresh
    | button that clears it.
    |
    */

    'cache' => [
        'enabled' => env('BONES_CACHE', true),
        'ttl' => env('BONES_CACHE_TTL', 600),
        'key' => 'bones.skeleton',
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */

    'routes' => [
        'hide_sensitive' => true,
        'sensitive_patterns' => [
            'telescope*',
            'horizon*',
            '_ignition*',
            'sanctum/csrf-cookie',
        ],

        // Wildcard-shadow warnings are skipped for routes matching these
        // patterns. Framework internals file their own routes in an order that
        // trips the check without anything you can act on.
        'shadow_ignore' => [
            'livewire-*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    */

    'views' => [
        // Extra absolute paths to scan in addition to resource_path('views').
        'paths' => [],

        // Scan views exposed by packages through view()->addNamespace().
        'include_namespaces' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Packages
    |--------------------------------------------------------------------------
    */

    'packages' => [
        // Also read package.json when it is present.
        'include_npm' => true,

        // Which installed packages are considered direct dependencies.
        'mark_direct' => true,

        // Ask Packagist whether a newer release exists for direct Composer
        // dependencies. Best-effort: failures are ignored and the result is
        // cached with the rest of the skeleton.
        'check_updates' => env('BONES_PACKAGE_UPDATES', true),

        // Ask Packagist for published security advisories affecting installed
        // packages. Best-effort, batched into one request per group of 50.
        'check_security' => env('BONES_PACKAGE_SECURITY', true),

        // Seconds to wait for each Packagist call before giving up.
        'timeout' => env('BONES_PACKAGE_TIMEOUT', 6),
    ],
];
