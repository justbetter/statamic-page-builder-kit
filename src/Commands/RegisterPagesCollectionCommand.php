<?php

namespace Justbetter\StatamicPageBuilderKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Site;
use Statamic\Facades\YAML;

class RegisterPagesCollectionCommand extends Command
{
    protected $signature = 'page-builder-kit:register-pages-collection';

    protected $description = 'Register the pages collection and blueprint';

    public function handle(): int
    {
        $this->bootCollections();
        $this->bootBlueprints();

        $this->info('Successfully registered the collection and blueprint.');

        return self::SUCCESS;
    }

    protected function bootCollections(): void
    {
        // Try to find the existing pages collection
        $pagesCollection = CollectionFacade::findByHandle('pages');

        // Create or update the pages collection if it doesn't exist or has no configuration file
        if (! $pagesCollection || ! File::exists($pagesCollection->path())) {
            if (! $pagesCollection) {
                $pagesCollection = CollectionFacade::make('pages');
            }

            // Set up the default configuration for the pages collection
            $pagesData = [
                'title' => __('Pages'),
                'sites' => array_keys(Site::all()->toArray()),
                'propagate' => false,
                'template' => 'statamic-page-builder-kit::page',
                'layout' => 'layout',
                'revisions' => false,
                'route' => '{parent_uri}/{slug}',
                'sort_dir' => 'asc',
                'preview_targets' => [
                    [
                        'label' => 'Entry',
                        'url' => '{permalink}',
                        'refresh' => true,
                    ],
                ],
                'structure' => [
                    'root' => true,
                ],
            ];

            // Write the configuration to the collection file
            $file = YAML::file($pagesCollection->path());
            File::put($pagesCollection->path(), $file->dump($pagesData));

            $this->info('Created pages collection.');
        }
    }

    protected function bootBlueprints(): void
    {
        $blueprint = Blueprint::find('collections.pages');

        if (! $blueprint || ! File::exists($blueprint->path())) {
            $blueprintContents = File::get(__DIR__.'/../../resources/blueprints/collections/pages/page.yaml');

            if (! File::exists(Blueprint::directory().'/collections/pages')) {
                File::makeDirectory(Blueprint::directory().'/collections/pages', 0755, true);
            }

            File::put(Blueprint::directory().'/collections/pages/page.yaml', $blueprintContents);

            $this->info('Created pages blueprint.');
        }
    }
}
