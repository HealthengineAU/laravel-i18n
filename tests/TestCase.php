<?php

namespace HealthEngine\I18n\Tests;

use HealthEngine\I18n\Providers\TranslationServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']['i18n.files.base_path'] = __DIR__ . '/resources/lang';
        $app['config']['i18n.files.namespaces'] = [ 'base', 'other' ];
        $app['config']['i18n.supported_languages'] = [ 'en', 'fr', 'de' ];
    }

    /**
     * Get package providers.
     *
     * @param  Application $app
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return [
            TranslationServiceProvider::class,
        ];
    }
}
