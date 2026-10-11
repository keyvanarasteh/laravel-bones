# Bones documentation

Bones reads the skeleton of a Laravel application and lays it out in one
dashboard. These pages explain each part and how to bend it to your needs.

| Page | What it covers |
| --- | --- |
| [Getting started](01-getting-started.md) | Install, enable and open the dashboard |
| [Routes](02-routes.md) | Every HTTP endpoint, filters, health, tree view |
| [View nodes](03-views.md) | Blade files and the graph of extends / includes |
| [Packages](04-packages.md) | Composer and npm dependencies and their flags |
| [Explore shelf](05-explore.md) | Curated packages and your own additions |
| [Configuration](06-configuration.md) | Every key in `config/bones.php` |
| [Extending](07-extending.md) | Write your own probe |

## The idea in one picture

```
        ┌────────────┐     ┌──────────┐
        │  routes    │     │ packages │
        └─────┬──────┘     └────┬─────┘
              │   ProbeRegistry │
              └────────┬────────┘
                       ▼
                  ┌─────────┐     ┌────────────┐
                  │ Skeleton│────▶│  dashboard │  /bones
                  └─────────┘     ├────────────┤
                       │          │ bones:scan │  console
                       └─────────▶└────────────┘
```

Everything you see in the browser is the same payload the console reads, so the
two can never fall out of step.
