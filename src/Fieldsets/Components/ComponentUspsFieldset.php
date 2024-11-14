<?php

namespace Justbetter\StatamicPageBuilderKit\Fieldsets\Components;

use Tdwesten\StatamicBuilder\Fieldset;
use Tdwesten\StatamicBuilder\FieldTypes\Replicator;
use Tdwesten\StatamicBuilder\FieldTypes\Set;
use Tdwesten\StatamicBuilder\FieldTypes\SetGroup;
use Tdwesten\StatamicBuilder\FieldTypes\Text;

class ComponentUspsFieldset extends Fieldset
{
    public function getTitle(): string
    {
        return __('Component - USP\'s');
    }

    public function getSlug(): string
    {
        return 'component_usps';
    }

    public function registerFields(): array
    {
        return [
            Replicator::make('usps', [
                SetGroup::make('usps', [
                    Set::make('usp', [
                        Text::make('title')->displayName(__('Title')),
                        Text::make('subtitle')->displayName(__('Subtitle')),
                    ])->displayName(__('USP'))
                ])->displayName(__('USP\'s')),
            ])->displayName(__('USP\'s')),
        ];
    }
}