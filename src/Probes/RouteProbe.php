<?php

namespace Keyvanarasteh\LaravelBones\Probes;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Keyvanarasteh\LaravelBones\Contracts\Probe;
use ReflectionClass;

class RouteProbe implements Probe
{
    /** @var array<string, array<int, string>>|null */
    protected ?array $middlewareGroups = null;

    public function __construct(protected Router $router)
    {
    }

    public function key(): string
    {
        return 'routes';
    }

    public function label(): string
    {
        return 'Routes';
    }

    public function description(): string
    {
        return 'Every HTTP endpoint the application exposes.';
    }

    public function icon(): string
    {
        return 'route';
    }

    public function scan(): array
    {
        $items = collect($this->router->getRoutes()->getRoutes())
            ->map(fn (Route $route) => $this->normalize($route))
            ->reject(fn (array $route) => $this->isSensitive($route['uri']))
            ->values()
            ->all();

        $items = $this->assessHealth($items);

        return [
            'summary' => $this->summarize($items),
            'items' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function normalize(Route $route): array
    {
        $action = $route->getAction();
        $handler = $action['controller'] ?? $action['uses'] ?? null;

        $parsed = $this->parseAction($handler);
        $middleware = $this->middleware($route);

        return [
            'id' => substr(md5($route->uri().'|'.implode(',', $route->methods())), 0, 12),
            'uri' => $route->uri(),
            'name' => $this->routeName($route->getName()),
            'methods' => array_values(array_diff($route->methods(), ['HEAD'])),
            'action' => $parsed,
            'middleware' => $middleware,
            'middleware_groups' => array_values(array_intersect($middleware, array_keys($this->middlewareGroups()))),
            'domain' => $route->domain(),
            'prefix' => $action['prefix'] ?? null,
            'namespace' => $action['namespace'] ?? null,
            'parameters' => $route->parameterNames(),
            'wheres' => $route->wheres ?: [],
            'is_closure' => $parsed['type'] === 'closure',
        ];
    }

    /**
     * A route-cached application assigns unnamed routes a random
     * "generated::<hash>" placeholder. It is noise, so treat it as unnamed.
     */
    protected function routeName(?string $name): ?string
    {
        if ($name === null || $name === '' || str_starts_with($name, 'generated::')) {
            return null;
        }

        return $name;
    }

    /**
     * @return array{type: string, class: ?string, method: ?string, full: ?string}
     */
    protected function parseAction(mixed $handler): array
    {
        if ($handler instanceof \Closure) {
            return ['type' => 'closure', 'class' => null, 'method' => null, 'full' => null];
        }

        // A route-cached application stores closures as serialized
        // SerializableClosure objects (or their string form). They are not
        // controllers, so never treat the blob as a class name.
        if (is_object($handler) || (is_string($handler) && $this->isSerializedClosure($handler))) {
            return ['type' => 'closure', 'class' => null, 'method' => null, 'full' => null];
        }

        if (! is_string($handler) || $handler === '') {
            return ['type' => 'callable', 'class' => null, 'method' => null, 'full' => null];
        }

        if (str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);

            return ['type' => 'controller', 'class' => $class, 'method' => $method, 'full' => $handler];
        }

        return ['type' => 'invokable', 'class' => $handler, 'method' => '__invoke', 'full' => $handler];
    }

    /**
     * A serialized closure surfaced by a route-cached application.
     */
    protected function isSerializedClosure(string $handler): bool
    {
        return str_contains($handler, 'SerializableClosure')
            || str_contains($handler, 'Laravel\\SerializableClosure')
            || (bool) preg_match('/^[OC]:\d+:"/', $handler);
    }

    /**
     * @return array<int, string>
     */
    protected function middleware(Route $route): array
    {
        $middleware = $route->getAction('middleware') ?? $route->middleware();

        if (is_string($middleware)) {
            $middleware = [$middleware];
        }

        return array_values(array_unique(array_filter((array) $middleware, 'is_string')));
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function middlewareGroups(): array
    {
        return $this->middlewareGroups ??= $this->router->getMiddlewareGroups();
    }

    /**
     * Flag routes whose static URI is captured by an earlier wildcard route,
     * and controllers that do not exist or do not implement the referenced
     * method.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    protected function assessHealth(array $items): array
    {
        $patterns = array_map(fn (array $route) => [
            'methods' => $route['methods'],
            'uri' => $route['uri'],
            'regex' => $this->uriRegex($route['uri']),
        ], $items);

        foreach ($items as $index => $route) {
            $issues = $this->healthCheck($route);

            if (! $this->ignoreShadow($route['uri'])) {
                for ($earlier = 0; $earlier < $index; $earlier++) {
                    $candidate = $patterns[$earlier];

                    if ($candidate['uri'] === $route['uri']) {
                        continue;
                    }

                    if (array_intersect($candidate['methods'], $route['methods']) === []) {
                        continue;
                    }

                    if ($this->ignoreShadow($candidate['uri'])) {
                        continue;
                    }

                    if (preg_match($candidate['regex'], $route['uri'])) {
                        $issues[] = sprintf(
                            'Shadowed by an earlier %s route matching [%s].',
                            implode('|', $candidate['methods']),
                            $candidate['uri'],
                        );
                        break;
                    }
                }
            }

            $items[$index]['health'] = [
                'ok' => $issues === [],
                'issues' => $issues,
            ];
        }

        return $items;
    }

    /**
     * Framework internals register their own files in an order that can look
     * shadowed without anything the application can act on.
     */
    protected function ignoreShadow(string $uri): bool
    {
        foreach (config('bones.routes.shadow_ignore', []) as $pattern) {
            if (fnmatch($pattern, $uri) || fnmatch($pattern.'/*', $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Turn a URI pattern into a matching regex — {param} becomes one segment.
     */
    protected function uriRegex(string $uri): string
    {
        $parts = preg_split('/(\{[^}]+\})/', $uri, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $regex = '';

        foreach ($parts as $part) {
            $regex .= preg_match('/^\{.*\}$/', $part) ? '[^/]+' : preg_quote($part, '#');
        }

        return '#^'.$regex.'$#';
    }

    /**
     * @param  array<string, mixed>  $route
     * @return array<int, string>
     */
    protected function healthCheck(array $route): array
    {
        $action = $route['action'];

        if ($action['type'] === 'closure' || $action['class'] === null) {
            return [];
        }

        $class = $action['class'];

        if (! class_exists($class)) {
            return ["Controller [{$class}] was not found."];
        }

        if ($action['method'] !== null && ! (new ReflectionClass($class))->hasMethod($action['method'])) {
            return ["Method [{$action['method']}] is missing on [{$class}]."];
        }

        return [];
    }

    protected function isSensitive(string $uri): bool
    {
        if (! config('bones.routes.hide_sensitive', true)) {
            return false;
        }

        foreach (config('bones.routes.sensitive_patterns', []) as $pattern) {
            if (fnmatch($pattern, $uri) || fnmatch($pattern.'/*', $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, int>
     */
    protected function summarize(array $items): array
    {
        $collection = collect($items);

        return [
            'total' => $collection->count(),
            'controllers' => $collection->pluck('action.class')->filter()->unique()->count(),
            'closures' => $collection->where('is_closure', true)->count(),
            'middleware' => $collection->pluck('middleware')->flatten()->unique()->count(),
            'groups' => $collection->pluck('middleware_groups')->flatten()->unique()->count(),
            'domains' => $collection->pluck('domain')->filter()->unique()->count(),
            'issues' => $collection->filter(fn ($route) => ! $route['health']['ok'])->count(),
        ];
    }
}
