<?php

namespace JaOcero\FilaChat\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use JaOcero\FilaChat\Enums\RoleType;
use JaOcero\FilaChat\Models\FilaChatRole;

class FilaChatCommand extends Command
{
    public $signature = 'filachat:install';

    public $description = 'Setup the FilaChat plugin';

    public function handle(): int
    {
        $this->info('Starting FilaChat setup...');
        $this->publishAssets();
        $this->runMigrations();
        $this->seedRoles();
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
            '--tag' => 'filachat',
        ]);

        $this->info('Assets published.');
    }

    protected function runMigrations()
    {
        $this->info('Running migrations...');
        Artisan::call('migrate');
        $this->info('Migrations completed.');
    }

    protected function seedRoles()
    {
        $this->info('Seeding roles...');

        $roles = [
            ['name' => RoleType::AGENT->value],
            ['name' => RoleType::USER->value],
        ];

        foreach ($roles as $role) {
            FilaChatRole::firstOrCreate($role);
        }

        $this->info('Roles seeded.');
    }
}
