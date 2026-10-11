# 3 · View nodes

The **Views** tab turns your Blade files into a graph you can actually read:
what is a layout, what is a component, and which file reaches for which.

## Sources

Bones scans, in order:

1. `resources/views` in the application.
2. Any extra path in `bones.views.paths`.
3. Views exposed by packages through `view()->addNamespace()` — shown with a
   `package::` prefix (disable with `bones.views.include_namespaces = false`).

## What each node carries

| Field | Meaning |
| --- | --- |
| Name | The dotted view name (`components.alert`) |
| Kind | `layout`, `component` or `view` |
| Path | The file path relative to its root |
| Lines / size | Quick sense of weight |
| Extends | What this view `@extends` |
| Includes | Every `@include`, `@each` and `@component` |
| Used by | The inverse — who extends or includes this view |

## Kinds

| Kind | Detected by |
| --- | --- |
| Layout | No `@extends`, and uses `@yield` or `@show` |
| Component | Lives under `components/`, or declares `@props` |
| View | Everything else |

## Graph view

Switch **List → Graph** to draw the references as a layered diagram. Layouts sit
at the top, children below them, and edges point from a view to what it reaches
for. Hover a node to light up its neighbours.
