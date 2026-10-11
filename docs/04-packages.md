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
| update | A newer release exists on Packagist (hover for the version) |
| advisory | Packagist lists a security advisory affecting the version |

The installed version sits under the package name, and when an update is
available the latest version is shown beside it.

## Updates & security

Bones asks Packagist two best-effort questions, cached with the rest of the
skeleton:

- **New releases** for direct Composer dependencies (`repo.packagist.org`).
- **Security advisories** for every installed package, batched into one request
  per 50 packages (`packagist.org/api/security-advisories`).

Both are optional and fail quietly — a slow or unreachable network never breaks
the dashboard. When an advisory exists, affected packages sort to the top, their
row is tinted red and a red banner summarises the count. Disable either with
`bones.packages.check_updates` / `bones.packages.check_security`.

## Filters

- **Source** — composer or npm.
- **Direct** — ignore the transitive tree.
- **Laravel** — just the packages that wire themselves into the framework.
- **Insecure** — only packages with an outstanding advisory (shown when any exist).
