<?php

namespace Justbetter\StatamicPageBuilderKit;

use Justbetter\StatamicPageBuilderKit\Commands\RegisterPagesCollectionCommand;
use Justbetter\StatamicPageBuilderKit\Traits\BootsPageBuilder;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    use BootsPageBuilder;

    protected $commands = [
        RegisterPagesCollectionCommand::class,
    ];

    public function bootAddon(): void
    {
        $this
            ->bootViews()
            ->bootPageBuilder();
    }

    public function bootViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'statamic-page-builder-kit');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/justbetter/statamic-page-builder-kit'),
            __DIR__.'/../resources/fieldsets' => resource_path('fieldsets/vendor/statamic-page-builder-kit'),
        ], 'statamic-page-builder-kit');

        return $this;
    }
}
