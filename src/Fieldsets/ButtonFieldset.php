<?php

namespace Justbetter\StatamicPageBuilderKit\Fieldsets;

use Tdwesten\StatamicBuilder\Fieldset;
use Tdwesten\StatamicBuilder\FieldTypes\Text;
use Tdwesten\StatamicBuilder\FieldTypes\Link;
use Tdwesten\StatamicBuilder\FieldTypes\Toggle;

class ButtonFieldset extends Fieldset
{
    public function registerFields(): array
    {
        return [
            Text::make('button_text')->displayName(__('Button text')),
            Link::make('button_link')->displayName(__('Button link')),
            Toggle::make('button_open_in_new_tab')->displayName(__('Button open in new tab'))
        ];
    }
}
