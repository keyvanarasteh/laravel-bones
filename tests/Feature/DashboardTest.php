<?php

namespace Keyvanarasteh\LaravelBones\Tests\Feature;

use Keyvanarasteh\LaravelBones\Tests\TestCase;

class DashboardTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('bones.enabled', true);
        $app['config']->set('bones.cache.enabled', false);
    }

    public function test_the_dashboard_renders(): void
    {
        $this->get('/bones')
            ->assertOk()
            ->assertSee('Laravel Bones')
            ->assertSee('data-panel="routes"', false)
            ->assertSee('data-panel="views"', false)
            ->assertSee('data-panel="packages"', false)
            ->assertSee('data-panel="explore"', false);
    }

    public function test_it_serves_the_skeleton_as_json(): void
    {
        $this->getJson('/bones/data')
            ->assertOk()
            ->assertJsonStructure(['data' => ['routes', 'views', 'packages'], 'explore']);
    }
}
