<?php

namespace HealthEngine\I18n\Http\Middleware\Detectors;

use HealthEngine\I18n\Contracts\LanguageDetector;
use Illuminate\Http\Request;

final class ParameterDetector implements LanguageDetector
{
    protected string $param;

    public function __construct(string $param)
    {
        $this->param = $param;
    }

    public function detect(Request $request): ?string
    {
        $lang = $request->input($this->param);
        return is_string($lang) ? $lang : null;
    }
}
