<?php

namespace Keyvanarasteh\LaravelBones\Tests\Feature;

use Keyvanarasteh\LaravelBones\Services\Skeleton;
use Keyvanarasteh\LaravelBones\Tests\TestCase;

class SkeletonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('bones.cache.enabled', false);
    }

    public function test_it_inspects_every_registered_probe(): void
    {
        $skeleton = $this->app->make(Skeleton::class)->inspect();

        $this->assertArrayHasKey('routes', $skeleton);
        $this->assertArrayHasKey('views', $skeleton);
        $this->assertArrayHasKey('packages', $skeleton);

        foreach ($skeleton as $section) {
            $this->assertArrayHasKey('summary', $section['data']);
            $this->assertArrayHasKey('items', $section['data']);
        }
    }

    public function test_it_limits_to_requested_sections(): void
    {
        $skeleton = $this->app->make(Skeleton::class)->inspect(['routes']);

        $this->assertSame(['routes'], array_keys($skeleton));
    }

    public function test_it_reports_totals(): void
    {
        $totals = $this->app->make(Skeleton::class)->totals(['routes']);

        $this->assertArrayHasKey('routes', $totals);
        $this->assertIsInt($totals['routes']);
    }
}
