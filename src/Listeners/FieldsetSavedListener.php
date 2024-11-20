<?php

namespace Justbetter\StatamicPageBuilderKit\Listeners;

use Illuminate\Support\Facades\Cache;
use Statamic\Events\FieldsetSaved;

class FieldsetSavedListener
{
    public function handle(FieldsetSaved $event): void
    {
        Cache::forget('statamic-page-builder-kit.page-builder-components');
    }
}
