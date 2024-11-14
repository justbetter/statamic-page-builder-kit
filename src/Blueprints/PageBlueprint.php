<?php

namespace Justbetter\StatamicPageBuilderKit\Blueprints;

use Justbetter\StatamicPageBuilderKit\Fieldsets\PageBuilderFieldset;
use Tdwesten\StatamicBuilder\Blueprint;
use Tdwesten\StatamicBuilder\FieldTypes\Assets;
use Tdwesten\StatamicBuilder\FieldTypes\Section;
use Tdwesten\StatamicBuilder\FieldTypes\Tab;
use Tdwesten\StatamicBuilder\FieldTypes\Text;

class PageBlueprint extends Blueprint
{
    public $title = 'Page';

    public $handle = 'page';

    public $hidden = false;

    public function registerTabs(): array
    {
        return [
            Tab::make('General', [
                Section::make('General', [
                    Text::make('title')
                        ->displayName('Title')
                        ->required(),
                    PageBuilderFieldset::make(),
                ]),
            ]),
        ];
    }
}
