<?php

namespace Justbetter\StatamicPageBuilderKit\Fieldsets\Components;

use Justbetter\StatamicPageBuilderKit\Enums\BardButtonTitle;
use Justbetter\StatamicPageBuilderKit\Fieldsets\ButtonFieldset;
use Tdwesten\StatamicBuilder\Fieldset;
use Tdwesten\StatamicBuilder\FieldTypes\Assets;
use Tdwesten\StatamicBuilder\FieldTypes\Bard;

class ComponentHeroBannerFieldset extends Fieldset
{
    public function getTitle(): string
    {
        return __('Component - Hero Banner');
    }

    public function getSlug(): string
    {
        return 'component_hero_banner';
    }

    public function registerFields(): array
    {
        return [
            Bard::make('title')
                ->buttons(BardButtonTitle::cases())
                ->allowSource(false),
            Bard::make('text')
                ->buttons(BardButtonTitle::cases())
                ->allowSource(false),
            ButtonFieldset::make(),
            Assets::make('background_image')
                ->displayName(__('Background image'))
                ->maxFiles(1)
                ->container('assets')
                ->required(),
        ];
    }
}
