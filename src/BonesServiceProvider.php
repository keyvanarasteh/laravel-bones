<?php

namespace Keyvanarasteh\LaravelBones;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\ServiceProvider;
use Keyvanarasteh\LaravelBones\Console\Commands\InstallCommand;
use Keyvanarasteh\LaravelBones\Console\Commands\ScanCommand;
use Keyvanarasteh\LaravelBones\Contracts\Probe;
use Keyvanarasteh\LaravelBones\Services\Skeleton;
use Keyvanarasteh\LaravelBones\Support\ProbeRegistry;

class BonesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bones.php', 'bones');

        $this->app->singleton(ProbeRegistry::class, function ($app) {
            $registry = new ProbeRegistry;

            foreach (config('bones.probes', []) as $class => $enabled) {
                if (! $enabled || ! class_exists($class)) {
                    continue;
                }

                $probe = $app->make($class);

                if ($probe instanceof Probe) {
                    $registry->register($probe);
                }
            }

            return $registry;
        });

        $this->app->singleton(Skeleton::class, function ($app) {
            return new Skeleton(
                $app->make(ProbeRegistry::class),
                $app->make(CacheRepository::class),
            );
        });

        $this->commands([
            InstallCommand::class,
            ScanCommand::class,
        ]);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bones');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/bones.php' => config_path('bones.php'),
            ], 'bones-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/bones'),
            ], 'bones-views');
        }

        if (config('bones.enabled', false)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }
}
