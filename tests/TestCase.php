<?php

namespace Justbetter\StatamicPageBuilderKit\Tests;

use Justbetter\StatamicPageBuilderKit\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
