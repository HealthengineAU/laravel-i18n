<?php

namespace Healthengine\I18n\Http\Middleware;

use Closure;
use Healthengine\I18n\Contracts\LanguageDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use UnexpectedValueException;

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

        $configLanguage = config('i18n.language');

        if (!is_string($configLanguage)) {
            throw new UnexpectedValueException(
                'Did not expect language config to be of type: ' . gettype($configLanguage)
            );
        }

        return $configLanguage;
    }

    private function setLanguage(string $lang): void
    {
        App::setLocale($lang);

        if ($this->saveCookie) {
            $cookie = config('i18n.http.parameter');
            $ttl = config('i18n.http.cookies.ttl');

            Cookie::queue($cookie, $lang, $ttl);
        }
    }
}
