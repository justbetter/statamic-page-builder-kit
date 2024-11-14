<?php

namespace Justbetter\StatamicPageBuilderKit\Collections;

use Statamic\Facades\Site;
use Tdwesten\StatamicBuilder\BaseCollection;

class Pages extends BaseCollection
{
    public static function handle(): string
    {
        return 'pages';
    }

    public function title(): string
    {
        return __('Pages');
    }

    public function blueprint(): string
    {
        return 'page';
    }

    public function route(): ?string
    {
        return '{parent_uri}/{slug}';
    }

    public function slugs(): bool
    {
        return true;
    }

    public function titleFormat(): null|string|array
    {
        return null;
    }

    public function mount(): ?string
    {
        return null;
    }

    public function date(): bool
    {
        return false;
    }

    public function sites(): array
    {
        return [Site::default()->handle()];
    }

    public function template(): ?string
    {
        return 'statamic-page-builder-kit::page';
    }

    public function layout(): ?string
    {
        return 'layout';
    }

    public function inject(): array
    {
        return [];
    }

    public function searchIndex(): string
    {
        return 'default';
    }

    public function revisionsEnabled(): bool
    {
        return false;
    }

    public function defaultPublishState(): bool
    {
        return true;
    }

    public function originBehavior(): string
    {
        return 'select';
    }

    public function structure(): ?array
    {
        return [
            'root' => true,
            'slugs' => true
        ];
    }

    public function sortBy(): ?string
    {
        return null;
    }

    public function sortDir(): ?string
    {
        return null;
    }

    public function taxonomies(): array
    {
        return [];
    }

    public function propagate(): ?bool
    {
        return null;
    }

    public function previewTargets(): array
    {
        return [];
    }

    public function autosave(): bool|int|null
    {
        return null;
    }

    public function futureDateBehavior(): ?string
    {
        return null;
    }

    public function pastDateBehavior(): ?string
    {
        return null;
    }
}
