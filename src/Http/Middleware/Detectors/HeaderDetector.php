<?php

namespace Healthengine\I18n\Http\Middleware\Detectors;

use Healthengine\I18n\Contracts\LanguageDetector;
use Healthengine\I18n\LanguageParser;
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

        $configSupportedLanguages = config('i18n.supported_languages');

        if (!is_array($configSupportedLanguages)) {
            throw new \UnexpectedValueException(
                'Did not expect supported languages config to be of type: ' . gettype($configSupportedLanguages)
            );
        }

        return LanguageParser::getPreferredLanguage($header, $configSupportedLanguages);
    }
}
