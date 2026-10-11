# 5 · Explore shelf

**Explore** is a curated shelf of packages worth knowing about, grouped by the
job they do: developer experience, testing, performance, security, API, frontend
and more.

For every entry Bones shows whether it is **already installed** in this app,
and at which version. The shelf ships with the package, so it improves as Bones
is updated — you never have to maintain it to benefit from it.

## Add your own

Drop entries into `config/bones.php` under `explore`. They are merged with the
built-in catalogue:

```php
'explore' => [
    [
        'name' => 'acme/team-package',
        'description' => 'The thing every project here reaches for.',
        'url' => 'https://github.com/acme/team-package',
        'category' => 'Team',
        'tags' => ['acme', 'internal'],
    ],
],
```

## Shape

| Key | Required | Notes |
| --- | --- | --- |
| `name` | yes | The `vendor/package` name used for the installed check |
| `description` | yes | One line |
| `url` | yes | Where the card links |
| `category` | yes | Drives the category filter |
| `tags` | no | Free-form, shown as chips |
