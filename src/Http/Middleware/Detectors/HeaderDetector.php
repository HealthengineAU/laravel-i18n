<?php

namespace HealthEngine\I18n\Http\Middleware\Detectors;

use HealthEngine\I18n\Contracts\LanguageDetector;
use HealthEngine\I18n\LanguageParser;
use Illuminate\Http\Request;

final class HeaderDetector implements LanguageDetector
{
    protected string $header;

    public function __construct(string $header)
    {
        $this->header = $header;
    }

    public function detect(Request $request): ?string
    {
        $header = $request->header($this->header);

        if ($header === null) {
            return null;
        }

        if (is_array($header)) {
            $header = implode(',', $header);
        }

        return LanguageParser::getPreferredLanguage($header, config('i18n.supported_languages'));
    }
}
