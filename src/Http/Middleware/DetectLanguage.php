<?php

namespace HealthEngine\I18n\Http\Middleware;

use Closure;
use HealthEngine\I18n\Contracts\LanguageDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class DetectLanguage
{
    /** @var array<LanguageDetector> */
    private array $detectors;
    private bool $saveCookie;

    /**
     * @param array<LanguageDetector> $detectors
     * @param bool $saveCookie
     */
    public function __construct(array $detectors, bool $saveCookie = true)
    {
        $this->detectors = $detectors;
        $this->saveCookie = $saveCookie;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->setLanguage($this->detectLanguage($request));

        return $next($request);
    }

    private function detectLanguage(Request $request): string
    {
        foreach ($this->detectors as $detector) {
            /** @var LanguageDetector $detector */
            $lang = $detector->detect($request);

            if (is_string($lang)) {
                return $lang;
            }
        }

        return config('i18n.language');
    }

    private function setLanguage(string $lang): void
    {
        app()->setLocale($lang);

        if ($this->saveCookie) {
            $cookie = config('i18n.http.parameter');
            $ttl = config('i18n.http.cookies.ttl');

            Cookie::queue($cookie, $lang, $ttl);
        }
    }
}
