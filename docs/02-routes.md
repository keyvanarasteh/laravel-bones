# 2 · Routes

The **Routes** tab lists every HTTP endpoint the application registers, minus
anything you have asked Bones to hide.

## What each route carries

| Field | Meaning |
| --- | --- |
| Methods | One badge per HTTP verb (`GET`, `POST`, …) |
| URI | The path, parameters included (`users/{user}`) |
| Name | The named route, if any |
| Action | `Controller@method`, an invokable class, or `Closure` |
| Middleware | Every middleware and middleware group attached |
| Domain | The route domain, when it is not the default |
| Prefix / namespace | From the route action, when set |
| Parameters | Names of URI parameters |

## Filters

- **Search** matches the URI, name, action and middleware.
- **Methods** narrows to a single verb.
- **Issues only** shows just the routes below.

## Health

Bones flags two genuinely useful problems:

| Flag | Meaning |
| --- | --- |
| Shadowed | A static route sits behind an earlier wildcard, so it can never match. `users/create` behind `users/{user}` is the classic case. |
| Missing | The controller class or the method does not exist any more. |

The overview tile turns amber the moment an issue appears.

## Tree view

Switch **Cards → Tree** to see routes grouped by URI segment. Click a segment to
expand it. It is the quickest way to read a large API surface.
