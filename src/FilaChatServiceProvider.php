<?php

namespace JaOcero\FilaChat;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use JaOcero\FilaChat\Commands\FilaChatCommand;
use JaOcero\FilaChat\Commands\FilaChatCreateAgentCommand;
use JaOcero\FilaChat\Livewire\ChatBox;
use JaOcero\FilaChat\Livewire\ChatList;
use JaOcero\FilaChat\Livewire\SearchConversation;
use JaOcero\FilaChat\Testing\TestsFilaChat;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilaChatServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filachat';

    public static string $viewNamespace = 'filachat';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands());

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile($configFileName);
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang/');
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filachat/{$file->getFilename()}"),
                ], 'filachat-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilaChat);

        // Livewire
        Livewire::component('filachat-chat-list', ChatList::class);
        Livewire::component('filachat-chat-box', ChatBox::class);
        Livewire::component('filachat-search-conversation', SearchConversation::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'jaocero/filachat';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filachat', __DIR__ . '/../resources/dist/components/filachat.js'),
            Css::make('filachat-styles', __DIR__ . '/../resources/css/filachat.css')->loadedOnRequest(),
            // Js::make('filachat-scripts', __DIR__ . '/../resources/dist/filachat.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilaChatCommand::class,
            FilaChatCreateAgentCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            '00001_create_filachat_agents_table',
            '00002_create_filachat_conversations_table',
            '00003_create_filachat_messages_table',
        ];
    }
}
