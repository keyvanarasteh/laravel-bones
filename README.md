# Laravel Bones

[![Latest Version on Packagist](https://img.shields.io/packagist/v/keyvanarasteh/laravel-bones.svg?style=flat-square)](https://packagist.org/packages/keyvanarasteh/laravel-bones)
[![Total Downloads](https://img.shields.io/packagist/dt/keyvanarasteh/laravel-bones.svg?style=flat-square)](https://packagist.org/packages/keyvanarasteh/laravel-bones)
[![PHP Version](https://img.shields.io/packagist/php-v/keyvanarasteh/laravel-bones.svg?style=flat-square)](https://packagist.org/packages/keyvanarasteh/laravel-bones)
[![Laravel](https://img.shields.io/badge/laravel-10%20%7C%2011%20%7C%2012%20%7C%2013-FF2D20?style=flat-square&logo=laravel&logoColor=white)](#requirements)
[![Tests](https://img.shields.io/github/actions/workflow/status/keyvanarasteh/laravel-bones/tests.yml?label=tests&style=flat-square)](https://github.com/keyvanarasteh/laravel-bones/actions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

> An X-ray for your Laravel application. See the bones — routes, view nodes and packages — at a glance.

Bones is a drop-in development dashboard that reads the skeleton of any Laravel app
and lays it out in one clean, searchable interface. Large codebases stop being
guesswork: you can see what routes exist, how your Blade views knit together, and
exactly which packages are holding the app up.

## What you get

| Tab | What it shows |
| --- | --- |
| **Overview** | Live counts and health signals across the whole skeleton |
| **Routes** | Every HTTP route: method, URI, action, middleware, domain, duplicates, dead controllers |
| **Views** | Blade view nodes as a navigable graph: extends, includes and orphaned files |
| **Packages** | Composer and npm dependencies, flagged as direct, dev or Laravel-aware |
| **Explore** | A curated shelf of packages worth knowing, cross-referenced with yours |

## Requirements

- PHP 8.2+
- Laravel 10, 11, 12 or 13

## Installation

```bash
composer require keyvanarasteh/laravel-bones --dev
```

Bones registers itself. Enable it in `.env`:

```env
BONES_ENABLED=true
```

Then visit **`/bones`**.

## Usage

```bash
# open the dashboard
open http://localhost:8000/bones

# or print the skeleton to the console
php artisan bones:scan

# inspect a single section, as JSON
php artisan bones:scan --only=routes --json
```

## Documentation

- [Getting started](docs/01-getting-started.md)
- [Routes](docs/02-routes.md)
- [View nodes](docs/03-views.md)
- [Packages](docs/04-packages.md)
- [Explore shelf](docs/05-explore.md)
- [Configuration](docs/06-configuration.md)
- [Extending: writing a probe](docs/07-extending.md)
- [Changelog](CHANGELOG.md)

## Contributing

Contributions are welcome. Open an issue or a pull request — the MIT licence keeps this a friendly, no-strings project.

## License

MIT. See [LICENSE.md](LICENSE.md).
