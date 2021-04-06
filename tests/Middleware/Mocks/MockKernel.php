<?php

namespace HealthEngine\I18n\Tests\Middleware\Mocks;

use HealthEngine\I18n\Http\Middleware\AcceptLanguage;
use HealthEngine\I18n\Http\Middleware\DetectLanguage;
use HealthEngine\I18n\Http\Middleware\HasLanguage;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class MockKernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var string[]
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, mixed>
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'has-lang' => HasLanguage::class,
        'accept-lang' => AcceptLanguage::class,
        'detect-lang' => DetectLanguage::class,
    ];
}
