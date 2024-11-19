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
            ->bootPageBuilder()
            ->bootCollections();
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
        $pagesCollection = CollectionFacade::findByHandle('pages');

        if (! $pagesCollection || ! File::exists($pagesCollection->path())) {
            if (! $pagesCollection) {
                $pagesCollection = CollectionFacade::make('pages');
            }

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

            $file = YAML::file($pagesCollection->path());
            File::put($pagesCollection->path(), $file->dump($pagesData));
        }

        if (! Blueprint::find('collections/pages/page')) {
            $pageBlueprint = Blueprint::make('collections/pages/page');
            File::copyDirectory(__DIR__.'/../resources/blueprints/collections/pages/', File::dirname($pageBlueprint->path()));
        }

        return $this;
    }

    public function bootPageBuilder(): self
    {
        $pageBuilderComponents = FieldsetFacade::all();
        $pageBuilderComponents = $pageBuilderComponents
            ->filter(fn ($fieldset) => $this->fieldsetIsComponent($fieldset))
            ->mapToGroups(fn ($fieldset) => [
                $this->getFieldsetGroup($fieldset) => [$this->getFieldsetName($fieldset) => $fieldset],
            ])
            ->toBase();

        $groups = $pageBuilderComponents
            ->map(fn ($fieldsets, $group) => [
                'display' => __(Str::headline($group)),
                'sets' => $this->getGroupFieldsets($fieldsets),
            ])->toArray();

        $pageBuilderFieldset = FieldsetFacade::find('statamic-page-builder-kit::page_builder');
        $pageBuilderContent = $pageBuilderFieldset?->contents();

        if (! empty($pageBuilderContent['fields']) && ! empty($pageBuilderContent['fields'][0]['field']['sets'])) {
            $pageBuilderContent['fields'][0]['field']['sets'] = $groups;
        }

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
