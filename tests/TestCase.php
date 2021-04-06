<?php

namespace HealthEngine\I18n\Tests;

use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;
use HealthEngine\I18n\TranslationServiceProvider;
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
     * @param  Application $app
     * @return string[]
     */
    protected function overrideApplicationProviders($app)
    {
        return [
            IlluminateTranslationServiceProvider::class => TranslationServiceProvider::class,
        ];
    }
}
