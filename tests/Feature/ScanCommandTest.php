<?php

namespace Keyvanarasteh\LaravelBones\Tests\Feature;

use Keyvanarasteh\LaravelBones\Tests\TestCase;

class ScanCommandTest extends TestCase
{
    public function test_it_scans_from_the_console(): void
    {
        $this->artisan('bones:scan', ['--only' => ['packages']])
            ->assertSuccessful();
    }

    public function test_it_emits_json(): void
    {
        $this->artisan('bones:scan', ['--only' => ['routes'], '--json' => true])
            ->expectsOutputToContain('"summary"')
            ->assertSuccessful();
    }
}
