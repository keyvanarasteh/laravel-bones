<?php

namespace Keyvanarasteh\LaravelBones\Tests\Unit;

use Keyvanarasteh\LaravelBones\Probes\ViewProbe;
use Keyvanarasteh\LaravelBones\Tests\TestCase;

class ViewProbeTest extends TestCase
{
    protected string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = $this->makeTempDirectory([
            'layout.blade.php' => "<html><body>@yield('content')</body></html>",
            'home.blade.php' => "@extends('layout')\n@section('content')hello@endsection",
            'components/badge.blade.php' => '<span @props([\'x\'])></span>',
            'orphan.blade.php' => '<p>nobody includes me</p>',
        ]);

        config()->set('bones.views.paths', [$this->dir]);
        config()->set('bones.views.include_namespaces', false);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->dir);

        parent::tearDown();
    }

    protected function probe(): ViewProbe
    {
        return $this->app->make(ViewProbe::class);
    }

    public function test_it_collects_view_nodes(): void
    {
        $items = collect($this->probe()->scan()['items'])->keyBy('name');

        $this->assertTrue($items->has('home'));
        $this->assertTrue($items->has('layout'));
        $this->assertTrue($items->has('components.badge'));
    }

    public function test_it_classifies_kinds(): void
    {
        $items = collect($this->probe()->scan()['items'])->keyBy('name');

        $this->assertSame('layout', $items['layout']['kind']);
        $this->assertSame('component', $items['components.badge']['kind']);
        $this->assertSame('view', $items['home']['kind']);
    }

    public function test_it_resolves_reverse_references(): void
    {
        $items = collect($this->probe()->scan()['items'])->keyBy('name');

        $this->assertContains('home', $items['layout']['used_by']);
        $this->assertContains('layout', $items['home']['extends']);
    }

    public function test_it_builds_a_graph(): void
    {
        $graph = $this->probe()->scan()['graph'];

        $this->assertNotEmpty($graph['nodes']);
        $this->assertNotEmpty($graph['edges']);
        $this->assertContains('layout', collect($graph['edges'])->pluck('to'));
    }
}
