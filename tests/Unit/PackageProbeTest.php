<?php

namespace Keyvanarasteh\LaravelBones\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Keyvanarasteh\LaravelBones\Probes\PackageProbe;
use Keyvanarasteh\LaravelBones\Tests\TestCase;

class PackageProbeTest extends TestCase
{
    protected string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = $this->makeTempDirectory([
            'composer.json' => json_encode([
                'require' => ['illuminate/support' => '^11.0'],
                'require-dev' => ['phpunit/phpunit' => '^11.0'],
            ]),
            'installed.json' => json_encode([
                'packages' => [
                    [
                        'name' => 'illuminate/support',
                        'version' => 'v11.0.0',
                        'description' => 'The Laravel framework.',
                        'license' => ['MIT'],
                        'extra' => ['laravel' => ['providers' => ['Illuminate\\Support\\ServiceProvider']]],
                    ],
                    [
                        'name' => 'phpunit/phpunit',
                        'version' => 'v11.0.0',
                        'description' => 'Testing framework.',
                        'license' => 'BSD-3-Clause',
                    ],
                ],
            ]),
            'package.json' => json_encode([
                'dependencies' => ['vue' => '^3.4.0'],
            ]),
        ]);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->dir);

        parent::tearDown();
    }

    protected function probe(): PackageProbe
    {
        return new class($this->app->make(Filesystem::class), $this->dir) extends PackageProbe
        {
            public function __construct(Filesystem $files, protected string $base)
            {
                parent::__construct($files);
            }

            protected function composerManifestPath(): string
            {
                return $this->base.'/composer.json';
            }

            protected function installedPath(): string
            {
                return $this->base.'/installed.json';
            }

            protected function npmManifestPath(): string
            {
                return $this->base.'/package.json';
            }
        };
    }

    public function test_it_reads_composer_and_npm_packages(): void
    {
        $data = $this->probe()->scan();

        $this->assertSame(3, $data['summary']['total']);
        $this->assertSame(2, $data['summary']['composer']);
        $this->assertSame(1, $data['summary']['npm']);
    }

    public function test_it_flags_direct_dev_and_laravel_packages(): void
    {
        $items = collect($this->probe()->scan()['items'])->keyBy('name');

        $this->assertTrue($items['illuminate/support']['direct']);
        $this->assertTrue($items['illuminate/support']['laravel']);
        $this->assertTrue($items['phpunit/phpunit']['dev']);
        $this->assertSame('MIT', $items['illuminate/support']['license']);
        $this->assertSame('vue', $items['vue']['name']);
    }
}
