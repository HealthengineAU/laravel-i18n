<?php

namespace Healthengine\I18n\Tests\Middleware;

use Healthengine\I18n\Tests\Middleware\Mocks\MockController;
use Healthengine\I18n\Tests\Middleware\Mocks\MockKernel;
use Healthengine\I18n\Tests\TestCase;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;

abstract class MiddlewareTestCase extends TestCase
{
    protected const DEFAULT_HEADERS = ['Accept-Language' => ''];

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableCookieEncryption();
    }

    /**
     * @param  Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(Kernel::class, MockKernel::class);
    }

    /**
     * Define routes setup.
     *
     * @param  Router   $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->group(['middleware' => 'has-lang'], function (Router $router): void {
            $router->get('/language/has/get', MockController::class);
            $router->post('/language/has/post', MockController::class);
        });

        $router->group(['middleware' => 'accept-lang'], function (Router $router): void {
            $router->get('/language/accept/get', MockController::class);
            $router->post('/language/accept/post', MockController::class);
        });

        $router->group(['middleware' => 'detect-lang'], function (Router $router): void {
            $router->get('/language/detect/get', MockController::class);
            $router->post('/language/detect/post', MockController::class);
        });
    }
}
