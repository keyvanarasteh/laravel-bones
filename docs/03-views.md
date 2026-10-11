# 3 · View nodes

The **Views** tab turns your Blade files into a graph you can actually read:
what is a layout, what is a component, and which file reaches for which.

## Sources

Bones scans, in order:

1. `resources/views` in the application.
2. Any extra path in `bones.views.paths`.
3. Views exposed by packages through `view()->addNamespace()` (disable with
   `bones.views.include_namespaces = false`).

Files found through more than one root are de-duplicated, and each root is
labelled with a friendly package name — a Composer `vendor/package` for vendor
views, `This app` (plus sub-folder) for your own, and the folder name otherwise.
That turns anonymous hash namespaces into something readable.

## The list

The **List** view groups views by package. Your own views come first and are
expanded; package groups start collapsed and open on click, so a project with
hundreds of vendor views stays scannable. Search expands every matching group.
Use the **Package** filter to focus on one package or on your own views.

## What each node carries

| Field | Meaning |
| --- | --- |
| Name | The dotted view name (`components.alert`) |
| Kind | `layout`, `component` or `view` |
| Path | The file path relative to its root |
| Lines | Quick sense of weight |
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
for. Hover a node to light up its neighbours. By default the graph shows only
views that take part in a reference; toggle **Isolated** to include the rest.
