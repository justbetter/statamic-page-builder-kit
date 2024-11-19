<?php

namespace Justbetter\StatamicPageBuilderKit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Fieldset as FieldsetFacade;
use Statamic\Facades\Site;
use Statamic\Facades\YAML;
use Statamic\Fields\Fieldset;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Str;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this
            ->bootConfig()
            ->bootViews()
            ->bootPageBuilder();

        if (config('justbetter.statamic-page-builder-kit.boot_collections', true)) {
            $this->bootCollections();
        }
    }

    public function bootConfig(): self
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/statamic-page-builder-kit.php',
            'justbetter.statamic-page-builder-kit'
        );

        return $this;
    }

    public function bootViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'statamic-page-builder-kit');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/justbetter/statamic-page-builder-kit'),
        ], 'statamic-page-builder-kit');

        return $this;
    }

    public function bootCollections(): self
    {
        // Try to find the existing pages collection
        $pagesCollection = CollectionFacade::findByHandle('pages');

        // Create or update the pages collection if it doesn't exist or has no configuration file
        if (! $pagesCollection || ! File::exists($pagesCollection->path())) {
            if (! $pagesCollection) {
                $pagesCollection = CollectionFacade::make('pages');
            }

            // Set up the default configuration for the pages collection
            $pagesData = [
                'title' => __('Pages'),
                'sites' => array_keys(Site::all()->toArray()),
                'propagate' => false,
                'template' => 'statamic-page-builder-kit::page',
                'layout' => 'layout',
                'revisions' => false,
                'route' => '{parent_uri}/{slug}',
                'sort_dir' => 'asc',
                'preview_targets' => [
                    [
                        'label' => 'Entry',
                        'url' => '{permalink}',
                        'refresh' => true,
                    ],
                ],
                'structure' => [
                    'root' => true,
                ],
            ];

            // Write the configuration to the collection file
            $file = YAML::file($pagesCollection->path());
            File::put($pagesCollection->path(), $file->dump($pagesData));
        }

        // Ensure the page blueprint exists, copy from package resources if it doesn't
        if (! Blueprint::find('collections/pages/page')) {
            $pageBlueprint = Blueprint::make('collections/pages/page');
            File::copyDirectory(__DIR__.'/../resources/blueprints/collections/pages/', File::dirname($pageBlueprint->path()));
        }

        return $this;
    }

    public function bootPageBuilder(): self
    {
        // Get all available fieldsets
        $pageBuilderComponents = FieldsetFacade::all();
        
        // Filter and group the fieldsets that are marked as page builder components
        $pageBuilderComponents = $pageBuilderComponents
            ->filter(fn ($fieldset) => $this->fieldsetIsComponent($fieldset))
            ->mapToGroups(fn ($fieldset) => [
                $this->getFieldsetGroup($fieldset) => [$this->getFieldsetName($fieldset) => $fieldset],
            ])
            ->toBase();

        // Format the groups for display in the UI
        $groups = $pageBuilderComponents
            ->map(fn ($fieldsets, $group) => [
                'display' => __(Str::headline($group)),
                'sets' => $this->getGroupFieldsets($fieldsets),
            ])->toArray();

        // Get the page builder fieldset from the package
        $pageBuilderFieldset = FieldsetFacade::find('statamic-page-builder-kit::page_builder');
        $pageBuilderContent = $pageBuilderFieldset?->contents();

        // Update the fieldset with the newly organized component groups
        if (! empty($pageBuilderContent['fields']) && ! empty($pageBuilderContent['fields'][0]['field']['sets'])) {
            $pageBuilderContent['fields'][0]['field']['sets'] = $groups;
        }

        // Save the updated fieldset configuration without triggering events
        $pageBuilderFieldset
            ?->setContents($pageBuilderContent ?? [])
            ?->saveQuietly();

        return $this;
    }

    protected function fieldsetIsComponent(Fieldset $fieldset): bool
    {
        $handle = $this->getFieldsetHandle($fieldset);

        return Str::startsWith($handle, 'component_');
    }

    protected function getGroupFieldsets(Collection $fieldsets): array
    {
        $sets = $fieldsets->mapWithKeys(function ($fieldset) {
            $fieldset = collect($fieldset)->first();

            return [
                $this->getFieldsetGroup($fieldset).'_'.$this->getFieldsetName($fieldset) => [
                    'display' => __(Str::headline($this->getFieldsetName($fieldset))),
                    'fields' => [
                        [
                            'import' => $fieldset->handle(),
                        ],
                    ],
                ],
            ];
        })->toBase();

        return $sets->toArray();
    }

    protected function getFieldsetGroup(Fieldset $fieldset): string
    {
        $handle = $this->getFieldsetHandle($fieldset);
        $group = explode('_', $handle);

        return $group[1] ?? '';
    }

    protected function getFieldsetName(Fieldset $fieldset): string
    {
        $handle = $this->getFieldsetHandle($fieldset);
        $parts = explode('_', $handle);

        return implode('_', array_slice($parts, 2));
    }

    protected function getFieldsetHandle(Fieldset $fieldset): string
    {
        $handle = $fieldset->handle();
        if ($fieldset->isNamespaced()) {
            $handle = Str::after($fieldset->handle(), '::');
        }

        return $handle;
    }
}
