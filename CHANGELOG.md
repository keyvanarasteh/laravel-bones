# Changelog

All notable changes to **Laravel Bones** are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.2] - 2026-10-11

### Fixed

- **No more `generated::<hash>` route names.** A route-cached application
  assigns every unnamed route a random placeholder name. The dashboard now
  treats these as unnamed, so cards show the real URI and tree rows read
  `unnamed` instead of a meaningless hash.

## [1.2.1] - 2026-10-11

### Fixed

- **Route health no longer cries wolf on a route-cached app.** Laravel stores
  closures as serialized `SerializableClosure` blobs once routes are cached;
  the probe treated that blob as a controller class and flagged every such
  route as "controller not found", while the card printed the raw serialized
  string as the action. Serialized closures are now recognised as closures and
  the action reads `Closure`.
- **The Views graph is visible again.** On a real project almost every view is
  isolated (no `extends`/`includes`), and hundreds of them piled into a single
  off-screen column, leaving the pane apparently blank. The graph now shows the
  connected views by default, reports how many isolated views are hidden, and
  offers an **Isolated** toggle to include them.
- Route and view cards no longer stretch to the tallest card in the row, and
  long action strings wrap instead of overflowing.

### Added

- A **copy** button on every route health issue, copying the issue text.
- Clearer result counts — each tab now reads `Showing N of M`.

## [1.2.0] - 2026-10-11

### Changed

- **Compact, professional UI** — a tighter header, smaller type scale, denser
  cards, tables and toolbars, and a small icon set throughout, so the dashboard
  fits far more on screen without losing legibility.
- The **Routes tree** now renders expanded by default, shows per-node route
  counts, and no longer repeats the URI segment as each row's label — rows show
  the method, route name and action instead. Added **Expand/Collapse all**.
- Filters gained a proper **segmented control** (Cards/Tree, List/Graph) and
  checkbox-style toggles; every section shows `shown / total` counts.
- Added keyboard shortcuts: `/` focuses the active search, `Esc` clears it.

### Fixed

- The **Views graph rendered empty** — nodes were matched against a key the
  view items never had, so no node or edge was ever drawn.
- The **theme toggle did nothing** — the switch was wired from a `<head>`
  script before the button existed. Theme is now applied early (no flash) and
  wired on boot; light/dark preference is persisted safely.
- Copying now falls back to a selection-based copy when the async clipboard
  API is unavailable (for example on plain HTTP).
- The graph arrow marker now follows the theme instead of a hard-coded colour.

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
