<?php

namespace Keyvanarasteh\LaravelBones\Tests;

use Keyvanarasteh\LaravelBones\BonesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Never reach out to Packagist from the test suite.
        config()->set('bones.packages.check_updates', false);
        config()->set('bones.packages.check_security', false);
    }

    protected function getPackageProviders($app): array
    {
        return [BonesServiceProvider::class];
    }

    /**
     * Create a throwaway directory, optionally seeded with files.
     *
     * @param  array<string, string>  $files
     */
    protected function makeTempDirectory(array $files = []): string
    {
        $dir = sys_get_temp_dir().'/bones-'.bin2hex(random_bytes(6));
        mkdir($dir, 0777, true);

        foreach ($files as $name => $contents) {
            $path = $dir.'/'.$name;

            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            file_put_contents($path, $contents);
        }

        return $dir;
    }

    protected function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }
}
