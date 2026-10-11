<?php

namespace Keyvanarasteh\LaravelBones\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Keyvanarasteh\LaravelBones\Tests\TestCase;

class MiddlewareConfigTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('bones.enabled', true);
        $app['config']->set('bones.cache.enabled', false);
        $app['config']->set('bones.middleware', ['web', 'auth']);
    }

    public function test_it_applies_the_configured_middleware(): void
    {
        $route = Route::getRoutes()->getByName('bones.dashboard');

        $this->assertNotNull($route);
        $this->assertContains('web', $route->middleware());
        $this->assertContains('auth', $route->middleware());
    }
}
