<?php

namespace Justbetter\StatamicPageBuilderKit\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Justbetter\StatamicPageBuilderKit\Listeners\FieldsetSavedListener;
use Statamic\Events\FieldsetSaved;
use Statamic\Facades\Fieldset as FieldsetFacade;
use Statamic\Fields\Fieldset;
use Statamic\Support\Str;

trait BootsPageBuilder
{
    public function bootPageBuilder(): self
    {
        $this->bootPageBuilderFieldset();
        $this->bootPageBuilderListeners();

        return $this;
    }

    public function bootPageBuilderFieldset(): self
    {
        $cacheKey = 'statamic-page-builder-kit.page-builder-components';

        $pageBuilderContent = Cache::rememberForever($cacheKey, function () {
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
            if (isset($pageBuilderContent['fields'][0]['field']['sets'])) {
                $pageBuilderContent['fields'][0]['field']['sets'] = $groups;
            }

            return $pageBuilderContent ?? [];
        });

        // Save the updated fieldset configuration without triggering events
        FieldsetFacade::find('statamic-page-builder-kit::page_builder')
            ?->setContents($pageBuilderContent)
            ?->saveQuietly();

        return $this;
    }

    public function bootPageBuilderListeners(): self
    {
        Event::listen(FieldsetSaved::class, FieldsetSavedListener::class);

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
