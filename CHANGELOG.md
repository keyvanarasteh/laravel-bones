# Changelog

All notable changes to **Laravel Bones** are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-10-11

### Added

- `BONES_MIDDLEWARE` (comma-separated) and `BONES_ROUTE_PREFIX` can now be set
  from the environment, so the dashboard can be mounted elsewhere (for example
  `api/bones`) and gated behind an existing guard (`web,auth`) without
  publishing config.

## [1.0.0] - 2026-10-11

First public release.

### Added

- **Dashboard** at `{prefix}` (default `/bones`) with Overview, Routes, Views,
  Packages and Explore tabs, embedded in a single self-contained page.
- **Probe architecture** — the `Probe` contract, `ProbeRegistry` and `Skeleton`
  aggregator. New sections are one class and register in config.
- **RouteProbe** — every HTTP endpoint with method, URI, name, action,
  middleware, domain, prefix, namespace and parameters; health flags for
  wildcard-shadowed routes and missing controllers or methods.
- **ViewProbe** — Blade view nodes classified as `layout`, `component` or `view`,
  with `extends` / `includes` / `used_by` references, plus a layered graph.
- **PackageProbe** — Composer and npm dependencies, flagged direct, dev, Laravel
  and abandoned, with version, licence and homepage.
- **Explore shelf** — a curated catalogue of packages, cross-referenced with what
  is installed; extendable from `config/bones.php`.
- **Console** — `bones:scan` (`--only`, `--json`) and `bones:install`.
- **Configuration** — off by default; `BONES_ENABLED`, route prefix, middleware,
  caching, sensitive-route hiding, scan paths and npm toggle.
- Support for Laravel 10, 11, 12 and 13 on PHP 8.2+.
