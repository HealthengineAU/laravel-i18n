<?php

namespace HealthEngine\I18n\Providers;

use HealthEngine\I18n\Http\Middleware\AcceptLanguage;
use HealthEngine\I18n\Http\Middleware\Detectors\CookieDetector;
use HealthEngine\I18n\Http\Middleware\Detectors\HeaderDetector;
use HealthEngine\I18n\Http\Middleware\Detectors\ParameterDetector;
use HealthEngine\I18n\Http\Middleware\DetectLanguage;
use HealthEngine\I18n\Http\Middleware\HasLanguage;
use HealthEngine\I18n\Translator\LanguageLoader;
use HealthEngine\I18n\Translator\Translator;
use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

final class TranslationServiceProvider extends IlluminateTranslationServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/i18n.php' => config_path('i18n.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang'),
        ]);
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/i18n.php',
            'i18n'
        );

        $this->registerLanguageLoader();
        $this->registerDetectors();
        $this->registerMiddlewares();

        $this->app->singleton('translator', function ($app): Translator {
            return new Translator(
                $app['i18n.loader'],
                $app['config']['i18n.language'],
                $app['config']['i18n.files.namespaces']
            );
        });

        $this->app->singleton('illuminate-translator', function ($app): Translator {
            return new Translator(
                $app['i18n.loader'],
                $app['config']['i18n.language'],
                $app['config']['i18n.files.namespaces']
            );
        });
    }

    protected function registerLanguageLoader(): void
    {
        $this->app->singleton('i18n.loader', function ($app): LanguageLoader {
            return new LanguageLoader($app['files'], $app['config']['i18n.files.base_path']);
        });
    }

    protected function registerDetectors(): void
    {
        $this->app->bind(
            ParameterDetector::class,
            fn ($app) => new ParameterDetector($app['config']['i18n.http.parameter'])
        );
        $this->app->bind(
            CookieDetector::class,
            fn ($app) => new CookieDetector($app['config']['i18n.http.parameter'])
        );

        $this->app->bind(
            HeaderDetector::class,
            fn ($app) => new HeaderDetector($app['config']['i18n.http.header'])
        );
    }

    protected function registerMiddlewares(): void
    {
        $this->app->bind(AcceptLanguage::class, function ($app): AcceptLanguage {
            $header = $app['config']['i18n.http.header'];

            return new AcceptLanguage($header);
        });

        $this->app->bind(HasLanguage::class, function ($app): HasLanguage {
            $param = $app['config']['i18n.http.parameter'];
            $cookiesEnabled = $app['config']['i18n.http.cookies.enabled'];

            return new HasLanguage($param, $cookiesEnabled);
        });


        $this->app->bind(DetectLanguage::class, function ($app): DetectLanguage {
            $detectorOrder = [
                $app[ParameterDetector::class],
                $app[HeaderDetector::class],
                $app[CookieDetector::class],
            ];

            $saveCookie = $app['config']['i18n.http.cookies.enabled'];

            return new DetectLanguage($detectorOrder, $saveCookie);
        });
    }
}
