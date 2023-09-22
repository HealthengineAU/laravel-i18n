<?php

namespace Healthengine\I18n\Tests\Middleware\Mocks;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;

class MockController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        /** @var string $key */
        $key = Request::input('key') ?? 'greeting.substitution.symphonyStyle';
        $value = i18n($key, Request::all());

        return new JsonResponse([
            'lang' => app()->getLocale(),
            'value' => $value,
        ]);
    }
}
