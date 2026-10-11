<?php

namespace Keyvanarasteh\LaravelBones\Tests\Unit;

use Illuminate\Routing\Router;
use Keyvanarasteh\LaravelBones\Probes\RouteProbe;
use Keyvanarasteh\LaravelBones\Tests\TestCase;

class RouteProbeTest extends TestCase
{
    protected function probe(): RouteProbe
    {
        return new RouteProbe($this->app->make(Router::class));
    }

    public function test_it_normalizes_a_route(): void
    {
        $this->app->make(Router::class)
            ->get('/api/users', fn () => 'ok')
            ->name('users.index')
            ->middleware('web');

        $route = collect($this->probe()->scan()['items'])->firstWhere('uri', 'api/users');

        $this->assertNotNull($route);
        $this->assertSame(['GET'], $route['methods']);
        $this->assertSame('users.index', $route['name']);
        $this->assertTrue($route['is_closure']);
        $this->assertContains('web', $route['middleware']);
    }

    public function test_it_flags_routes_shadowed_by_earlier_wildcards(): void
    {
        $router = $this->app->make(Router::class);
        $router->get('users/{user}', fn () => 'show');
        $router->get('users/create', fn () => 'create');

        $data = $this->probe()->scan();
        $shadowed = collect($data['items'])->firstWhere('uri', 'users/create');

        $this->assertFalse($shadowed['health']['ok']);
        $this->assertStringContainsString('Shadowed', $shadowed['health']['issues'][0]);
    }

    public function test_it_flags_missing_controllers(): void
    {
        $this->app->make(Router::class)
            ->get('broken', 'App\\Http\\Controllers\\MissingController@index');

        $route = collect($this->probe()->scan()['items'])->firstWhere('uri', 'broken');

        $this->assertFalse($route['health']['ok']);
        $this->assertStringContainsString('was not found', $route['health']['issues'][0]);
    }

    public function test_it_treats_serialized_closures_as_closures(): void
    {
        $probe = new class($this->app->make(Router::class)) extends RouteProbe {
            public function action(string $handler): array
            {
                return $this->parseAction($handler);
            }
        };

        $serialized = 'O:55:"Laravel\\SerializableClosure\\SerializableClosure":1:{s:3:"use";a:0:{}}';
        $parsed = $probe->action($serialized);

        $this->assertSame('closure', $parsed['type']);
        $this->assertNull($parsed['class']);
        $this->assertNull($parsed['full']);
    }

    public function test_it_hides_sensitive_routes(): void
    {
        config()->set('bones.routes.hide_sensitive', true);
        config()->set('bones.routes.sensitive_patterns', ['telescope*']);

        $this->app->make(Router::class)->get('telescope/requests', fn () => 'nope');
        $this->app->make(Router::class)->get('public/ping', fn () => 'ok');

        $uris = collect($this->probe()->scan()['items'])->pluck('uri');

        $this->assertNotContains('telescope/requests', $uris);
        $this->assertContains('public/ping', $uris);
    }
}
