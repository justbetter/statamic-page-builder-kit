<?php

namespace Justbetter\StatamicPageBuilderKit\Fieldsets;

use Tdwesten\StatamicBuilder\Fieldset;
use Tdwesten\StatamicBuilder\FieldTypes\Replicator;
use Tdwesten\StatamicBuilder\FieldTypes\Set;
use Tdwesten\StatamicBuilder\FieldTypes\SetGroup;
use TorMorten\Eventy\Facades\Eventy;

class PageBuilderFieldset extends Fieldset
{
    public function getTitle(): string
    {
        return __('Page Builder');
    }

    public function getSlug(): string
    {
        return 'component_page_builder';
    }

    public function registerFields(): array
    {
        // Load configuration groups and sets
        $groups = collect(config('justbetter.statamic-page-builder-kit.groups', []));
        $baseSets = collect(config('justbetter.statamic-page-builder-kit.sets', []));
        $additionalSets = collect(config('justbetter.statamic-page-builder-kit.additional_sets', []));
        $sets = $baseSets->merge($additionalSets);

        // Allow other packages to filter the available sets
        $sets = Eventy::filter('justbetter.page_builder.sets', $sets);

        // Map configuration groups to Statamic SetGroup instances
        $statamicGroups = $groups->map(function ($group, $groupSlug) use ($sets) {
            // Map each set to a Statamic Set instance
            $groupSets = collect($sets->get($groupSlug, []))
                ->map(function ($set) {
                    $setComponent = new $set;
                    return Set::make($setComponent->getSlug(), [$setComponent::make()])
                        ->displayName($setComponent->getTitle());
                });

            // Create a SetGroup for the current group
            return SetGroup::make($groupSlug, $groupSets->toArray())
                ->displayName(__($group['title'] ?? ''));
        })->values();

        // Return the Replicator field containing the configured groups
        return [
            Replicator::make('page_builder', $statamicGroups->toArray())
        ];
    }
}