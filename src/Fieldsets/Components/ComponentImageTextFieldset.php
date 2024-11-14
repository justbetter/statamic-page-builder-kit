<?php

namespace Justbetter\StatamicPageBuilderKit\Fieldsets\Components;

use Justbetter\StatamicPageBuilderKit\Enums\BardButtonTitle;
use Tdwesten\StatamicBuilder\Fieldset;
use Tdwesten\StatamicBuilder\FieldTypes\Bard;
use Tdwesten\StatamicBuilder\FieldTypes\Assets;
use Statamic\Facades\Fieldset as FieldsetFacade;

class ComponentImageTextFieldset extends Fieldset
{
    public function getTitle(): string
    {
        return __('Component - Image + Text');
    }

    public function getSlug(): string
    {
        return 'component_image_text';
    }

    public function registerFields(): array
    {
        return [
            Bard::make('text')
                ->buttons(BardButtonTitle::cases())
                ->allowSource(false),
            Assets::make('image')
                ->container('assets')
                ->maxFiles(1)
        ];
    }
}
