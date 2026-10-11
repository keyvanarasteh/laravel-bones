# Laravel Bones — delivery index

## Goal

Build `keyvanarasteh/laravel-bones`: a small, professional, MIT dev-tool package that
visualises the skeleton of **any** Laravel application — HTTP routes, Blade view
nodes, and installed packages — in one clean dashboard, with a curated
"Explore" shelf of packages worth trying later.

## Constraints

- Works against any Laravel 10/11/12 app, installed as a dev dependency.
- Off by default; opt-in via `BONES_ENABLED`.
- No build step, no CDN, no hard dependency beyond Illuminate.
- Visual language matches the house style: dark-first, blue-600 accent, method
  badges, stat tiles, card layout.
- Deliberately re-architected (probe registry + skeleton aggregator) rather than
  a renamed clone of any prior internal tool.

## Batches

| Batch | Deliverable | Artifact |
| --- | --- | --- |
| 0 | Scaffold: composer, license, config, contract, registry, aggregator, provider | committed |
| 1 | Probes (Routes, Views, Packages), controller, routes, commands, tests | committed |
| 2 | Dashboard UI: Overview / Routes / Views / Packages / Explore tabs | committed |
| 3 | Docs, Explore catalog, README polish | committed |

## File map

```
config/bones.php
routes/web.php
resources/views/            dashboard + partials
src/
  BonesServiceProvider.php
  Contracts/Probe.php
  Support/ProbeRegistry.php
  Support/PackageCatalog.php
  Services/Skeleton.php
  Probes/RouteProbe.php
  Probes/ViewProbe.php
  Probes/PackageProbe.php
  Http/Controllers/BonesController.php
  Console/Commands/InstallCommand.php
  Console/Commands/ScanCommand.php
tests/
  TestCase.php
  Unit/*.php
  Feature/*.php
docs/                        user documentation
```
