# 6 · Configuration

Published with `php artisan bones:install` to `config/bones.php`.

## Top level

| Key | Default | Notes |
| --- | --- | --- |
| `enabled` | `env('BONES_ENABLED', false)` | Master switch |
| `route_prefix` | `bones` | Where the dashboard lives |
| `middleware` | `['web']` | Add `auth` or your own guard |

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

## Views

| Key | Default | Notes |
| --- | --- | --- |
| `views.paths` | `[]` | Extra absolute paths to scan |
| `views.include_namespaces` | `true` | Include package namespaces |

## Packages

| Key | Default | Notes |
| --- | --- | --- |
| `packages.include_npm` | `true` | Also read `package.json` |
