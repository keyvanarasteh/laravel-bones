# 7 · Extending: writing a probe

A **probe** inspects one facet of the application. Add one and Bones grows a new
tab without any change to the dashboard.

## The contract

```php
namespace App\Bones;

use Keyvanarasteh\LaravelBones\Contracts\Probe;

class EventProbe implements Probe
{
    public function key(): string        { return 'events'; }
    public function label(): string      { return 'Events'; }
    public function description(): string{ return 'Registered events and listeners.'; }
    public function icon(): string       { return 'graph'; }

    public function scan(): array
    {
        return [
            'summary' => ['total' => count(Event::all())],
            'items'   => collect(Event::all())
                ->map(fn ($event) => ['event' => $event])
                ->all(),
        ];
    }
}
```

## Register it

```php
// config/bones.php
'probes' => [
    // ...
    \App\Bones\EventProbe::class => true,
],
```

That is the whole integration. The tab, the count badge, the JSON endpoint, the
overview tile and `bones:scan` all pick it up automatically.

## The payload

`scan()` returns three keys:

| Key | Required | Shape |
| --- | --- | --- |
| `summary` | yes | Flat `string => scalar` map for tiles and counts |
| `items` | yes | The records the tab renders |
| `graph` | no | `['nodes' => [...], 'edges' => [...]]` for a diagram |

## Icons

`icon()` names a symbol from `resources/views/partials/icons.blade.php`
(`route`, `layout`, `package`, `graph`, `layers`, `grid`, `sparkles`, …). Adding
your own symbol there is a two-line change.
