# 4 · Packages

The **Packages** tab lists what is holding the app up, from two ecosystems.

## Sources

| Ecosystem | Read from |
| --- | --- |
| `composer` | `composer.json` and `vendor/composer/installed.json` |
| `npm` | `package.json` (disable with `bones.packages.include_npm = false`) |

Composer entries come with version, description, licence and homepage. npm
entries carry the dependency constraint and link to the npm registry.

## Flags

| Flag | Meaning |
| --- | --- |
| direct | Listed in `require` / `dependencies` |
| dev | Listed in `require-dev` / `devDependencies` |
| laravel | Registers a service provider via `extra.laravel.providers` |
| abandoned | Composer marks the package as abandoned |

## Filters

- **Source** — composer or npm.
- **Direct only** — ignore the transitive tree.
- **Laravel only** — just the packages that wire themselves into the framework.
