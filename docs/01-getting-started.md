# 1 · Getting started

## Install

```bash
composer require keyvanarasteh/laravel-bones --dev
```

Bones is a development tool, so it belongs in `require-dev`. It registers its
service provider through Laravel package discovery — no manual wiring.

## Enable

Bones stays off everywhere, including local, until you ask for it:

```env
BONES_ENABLED=true
```

That is deliberate: a route dashboard is useful, but nobody should be able to
reach it by accident in production.

## Open it

Browse to the prefix — `bones` by default:

```
https://your-app.test/bones
```

Prefer the terminal?

```bash
php artisan bones:scan                 # a tidy table per section
php artisan bones:scan --only=routes   # just one section
php artisan bones:scan --json          # machine-readable
```

## Publish (optional)

Only needed if you want to edit the config or theme the views:

```bash
php artisan bones:install              # publishes config + views
php artisan bones:install --force      # overwrite what exists
```

## What to read next

- [Routes](02-routes.md)
- [View nodes](03-views.md)
- [Packages](04-packages.md)
