<?php

namespace Justbetter\StatamicPageBuilderKit\Fieldsets\Components;

use Justbetter\StatamicPageBuilderKit\Enums\BardButtonTitle;
use Tdwesten\StatamicBuilder\Fieldset;
use Tdwesten\StatamicBuilder\FieldTypes\Bard;

class ComponentTextFieldset extends Fieldset
{
    public function getTitle(): string
    {
        return __('Component - Text');
    }

    public function getSlug(): string
    {
        return 'component_text';
    }

    public function registerFields(): array
    {
        return [
            Bard::make('text')
                ->buttons(BardButtonTitle::cases())
                ->allowSource(false)
        ];
    }
}
