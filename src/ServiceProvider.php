<?php

namespace Justbetter\StatamicPageBuilderKit;

use Illuminate\Support\Facades\File;
use Statamic\Providers\AddonServiceProvider;
use Justbetter\StatamicPageBuilderKit\Blueprints\PageBlueprint;

class ServiceProvider extends AddonServiceProvider
{
    protected array $baseCollections = [];
    protected array $baseFieldsets = [];

    public function register(): void
    {
        parent::register();
        $this->registerPageBuilder();
    }

    public function registerPageBuilder(): self
    {
        $this->baseCollections = $this->getClassesByPath(
            __DIR__ . '/Collections',
            'Justbetter\\StatamicPageBuilderKit\\'
        );

        $this->baseFieldsets = $this->getClassesByPath(
            __DIR__ . '/Fieldsets',
            'Justbetter\\StatamicPageBuilderKit\\'
        );

        return $this;
    }

    public function bootAddon(): void
    {
        $this
            ->bootConfig()
            ->bootViews();

        config(['statamic.builder.collections' => array_merge(
            $this->baseCollections,
            config('statamic.builder.collections') ?? []
        )]);

        config(['statamic.builder.blueprints' => [
            'collections.pages' => [
                'page' => PageBlueprint::class,
            ],
        ]]);

        config(['statamic.builder.fieldsets' => array_merge(
            $this->baseFieldsets,
            config('statamic.builder.fieldsets') ?? []
        )]);
    }

    public function bootConfig(): self
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/statamic-page-builder-kit.php',
            'justbetter.statamic-page-builder-kit'
        );

        return $this;
    }

    public function bootViews(): self
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'statamic-page-builder-kit');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/justbetter/statamic-page-builder-kit'),
        ], 'statamic-page-builder-kit');

        return $this;
    }

    protected function getClassesByPath(string $path, string $classPath): array
    {
        $classes = [];
        foreach (File::allFiles($path) as $file) {
            $relativePath = str_replace([$path . '/', '.php'], '', $file->getPathname());
            $fieldsetClass = $classPath . basename($path) . '\\' . str_replace('/', '\\', $relativePath);
            if (class_exists($fieldsetClass)) {
                $classes[] = $fieldsetClass;
            }
        }
        return $classes;
    }
}