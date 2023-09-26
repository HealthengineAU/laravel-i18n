<?php

use Healthengine\I18n\Translator\Translator;

if (! function_exists('i18n')) {

    /**
     * Translate a string, including substitutions for variables and markup.
     *
     * @param string $key
     * @param array<string, string> $vars (e.g. ['name' => 'Matthew'])
     * @param ?string $lang
     * @param bool $fallback
     * @param string[] $markup (e.g. ['<a href="#page">', '</a>'])
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

if (! function_exists('i18n_dir')) {

    /**
     * @return string The preferred content direction for the current language/locale.
     */
    function i18n_dir(): string
    {
        /** @var Translator $translator */
        $translator = app('translator');

        return $translator->direction();
    }
}
