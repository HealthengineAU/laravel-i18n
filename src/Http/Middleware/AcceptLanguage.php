<?php

namespace HealthEngine\I18n\Http\Middleware;

use HealthEngine\I18n\Http\Middleware\Detectors\HeaderDetector;

final class AcceptLanguage extends DetectLanguage
{
    public function __construct(string $header)
    {
        parent::__construct([new HeaderDetector($header)], false);
    }
}
