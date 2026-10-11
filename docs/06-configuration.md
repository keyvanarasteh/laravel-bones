# 6 · Configuration

Published with `php artisan bones:install` to `config/bones.php`.

## Top level

| Key | Default | Notes |
| --- | --- | --- |
| `enabled` | `env('BONES_ENABLED', false)` | Master switch |
| `route_prefix` | `env('BONES_ROUTE_PREFIX', 'bones')` | Where the dashboard lives |
| `middleware` | `env('BONES_MIDDLEWARE', 'web')` | Comma-separated; add `auth` or your own guard |

The `middleware` and `route_prefix` values can be set without publishing config:

```env
BONES_ROUTE_PREFIX=api/bones
BONES_MIDDLEWARE=web,auth
```

This is how Bones is gated when it is enabled somewhere other than local — for
example behind an existing admin session: `BONES_MIDDLEWARE=web,my.admin.gate`.

## Probes

```php
'probes' => [
    \Keyvanarasteh\LaravelBones\Probes\RouteProbe::class => true,
    \Keyvanarasteh\LaravelBones\Probes\ViewProbe::class => true,
    \Keyvanarasteh\LaravelBones\Probes\PackageProbe::class => true,
],
```

Set a value to `false` to drop a section. Append your own class — see
[Extending](07-extending.md).

## Cache

| Key | Default | Notes |
| --- | --- | --- |
| `cache.enabled` | `env('BONES_CACHE', true)` | Cache the combined skeleton |
| `cache.ttl` | `600` | Seconds |
| `cache.key` | `bones.skeleton` | Cache key |

The **Rescan** button (and `?fresh=1`) bypasses the cache.

## Routes

| Key | Default | Notes |
| --- | --- | --- |
| `routes.hide_sensitive` | `true` | Hide matches from the dashboard |
| `routes.sensitive_patterns` | telescope, horizon, … | `fnmatch` patterns |
| `routes.shadow_ignore` | `['livewire-*']` | Skip wildcard-shadow warnings for these URIs |

## Views

| Key | Default | Notes |
| --- | --- | --- |
| `views.paths` | `[]` | Extra absolute paths to scan |
| `views.include_namespaces` | `true` | Include package namespaces |

## Packages

| Key | Default | Notes |
| --- | --- | --- |
| `packages.include_npm` | `true` | Also read `package.json` |
| `packages.check_updates` | `env('BONES_PACKAGE_UPDATES', true)` | Ask Packagist for newer releases |
| `packages.check_security` | `env('BONES_PACKAGE_SECURITY', true)` | Ask Packagist for security advisories |
| `packages.timeout` | `env('BONES_PACKAGE_TIMEOUT', 6)` | Seconds to wait per Packagist call |
