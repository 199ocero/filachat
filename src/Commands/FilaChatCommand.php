<?php

namespace JaOcero\FilaChat\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FilaChatCommand extends Command
{
    public $signature = 'filachat:install';

    public $description = 'Install the FilaChat plugin';

    public function handle(): int
    {
        $this->info('Starting FilaChat installation...');
        $this->publishAssets();
        $this->runMigrations();
        $this->comment('All done');

        return self::SUCCESS;
    }

    protected function publishAssets()
    {
        $this->info('Publishing assets...');

        // Publish migrations
        Artisan::call('vendor:publish', [
            '--provider' => 'JaOcero\FilaChat\FilaChatServiceProvider',
            '--tag' => 'filachat-migrations',
        ]);

        // Publish configuration
        Artisan::call('vendor:publish', [
            '--provider' => 'JaOcero\FilaChat\FilaChatServiceProvider',
            '--tag' => 'filachat-config',
        ]);

        $this->info('Assets published.');
    }

    protected function runMigrations()
    {
        $this->info('Running migrations...');
        Artisan::call('migrate');
        $this->info('Migrations completed.');
    }
}
