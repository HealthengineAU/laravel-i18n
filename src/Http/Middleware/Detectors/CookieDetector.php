<?php

namespace Healthengine\I18n\Http\Middleware\Detectors;

use Healthengine\I18n\Contracts\LanguageDetector;
use Illuminate\Http\Request;

final class CookieDetector implements LanguageDetector
{
    protected string $cookie;

    public function __construct(string $cookie)
    {
        $this->cookie = $cookie;
    }

    public function detect(Request $request): ?string
    {
        $cookie = $request->cookie($this->cookie);
        return is_string($cookie) ? $cookie : null;
    }
}
