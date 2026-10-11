<?php

namespace Keyvanarasteh\LaravelBones\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'bones:install {--force : Overwrite files that already exist}';

    protected $description = 'Publish the Laravel Bones configuration and views';

    public function handle(): int
    {
        $this->components->info('Installing Laravel Bones');

        $this->call('vendor:publish', [
            '--tag' => 'bones-config',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'bones-views',
            '--force' => $this->option('force'),
        ]);

        $this->newLine();
        $this->components->info('Bones installed.');
        $this->components->bulletList([
            'Add <fg=blue>BONES_ENABLED=true</> to your .env',
            'Visit <fg=blue>/'.config('bones.route_prefix', 'bones').'</> in the browser',
            'Or run <fg=blue>php artisan bones:scan</> from the terminal',
        ]);

        return Command::SUCCESS;
    }
}
