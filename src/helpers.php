<?php

use HealthEngine\I18n\Translator\Translator;

if (! function_exists('i18n')) {

    /**
     * Translate a string, including substitutions for variables and markup.
     *
     * @param string $key
     * @param array<string, string> $vars (e.g. ['name' => 'Matthew'])
     * @param string[] $markup (e.g. ['<a href="#page">', '</a>'])
     * @param ?string $lang
     * @param bool $fallback
     * @return string
     */
    function i18n(
        string $key,
        array $vars = [],
        array $markup = [],
        ?string $lang = null,
        bool $fallback = true
    ): string {
        /** @var Translator $translator */
        $translator = app('translator');

        return $translator->get($key, $vars, $lang, $fallback, $markup);
    }
}
