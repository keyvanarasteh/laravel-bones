# Plan

## Architecture

Bones is a registry of **probes**. A probe is a small class that inspects one
facet of the application and returns a normalised section:

```php
interface Probe
{
    public function key(): string;         // "routes"
    public function label(): string;       // "Routes"
    public function description(): string; // one sentence
    public function icon(): string;        // glyph name
    public function scan(): array;         // ['summary' => [...], 'items' => [...]]
}
```

`ProbeRegistry` holds the enabled probes. `Skeleton` runs them and caches the
combined payload. The dashboard and the `bones:scan` command both consume the
same payload, so the CLI and the UI can never disagree.

Adding a section later (models, events, jobs, schedule…) means writing one probe
and registering it in `config/bones.php` — no changes to the UI.

## Why not a single scanner

A single monolithic scanner couples every facet together and grows without bound.
Probes keep each concern isolated, testable in isolation, and easy for the
community to contribute.

## Data contract

Every section returns:

```php
[
    'summary' => ['total' => 42, 'issues' => 0, ...],
    'items'   => [ /* normalised records */ ],
    'graph'   => ['nodes' => [...], 'edges' => [...]], // optional
]
```

## UI

The dashboard embeds the skeleton as JSON and renders client-side. One page load,
zero extra requests for filtering — fast even against large apps. Tabs:
Overview, Routes, Views, Packages, Explore.

## Verification

- PHPUnit via Orchestra Testbench, one test file per probe plus a dashboard test.
- `php artisan bones:scan` smoke-tested against a fixture app in tests.
