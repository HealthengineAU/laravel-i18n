<?php

namespace Healthengine\I18n\Http\Middleware;

use Healthengine\I18n\Http\Middleware\Detectors\CookieDetector;
use Healthengine\I18n\Http\Middleware\Detectors\ParameterDetector;

final class HasLanguage extends DetectLanguage
{
    public function __construct(string $param, bool $cookiesEnabled = true)
    {
        $detectors = [new ParameterDetector($param)];

        if ($cookiesEnabled) {
            $detectors[] = new CookieDetector($param);
        }

        parent::__construct($detectors, $cookiesEnabled);
    }
}
