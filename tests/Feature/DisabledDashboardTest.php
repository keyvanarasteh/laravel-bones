<?php

namespace Keyvanarasteh\LaravelBones\Tests\Feature;

use Keyvanarasteh\LaravelBones\Tests\TestCase;

class DisabledDashboardTest extends TestCase
{
    public function test_it_is_disabled_by_default(): void
    {
        $this->assertFalse(config('bones.enabled'));

        $this->get('/bones')->assertNotFound();
    }
}
