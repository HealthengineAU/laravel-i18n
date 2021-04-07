<?php

namespace HealthEngine\I18n\Translator;

use HealthEngine\I18n\LanguageParser;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Enables the loading of fallback language for missing
 * keys when using JSON configuration instead of PHP files.
 */
final class Translator implements TranslatorContract
{
    private string $currentLanguage;
    private string $fallbackLanguage;
    private LanguageLoader $loader;

    /** @var string[] */
    private array $namespaces;

    /** @var array<mixed> Loaded language files */
    private array $loaded = [];

    /**
     * @param LanguageLoader $loader
     * @param string $defaultLanguage
     * @param string[] $namespaces
     */
    public function __construct(LanguageLoader $loader, string $defaultLanguage, array $namespaces)
    {
        $this->currentLanguage = $defaultLanguage;
        $this->fallbackLanguage = $defaultLanguage;
        $this->namespaces = $namespaces;
        $this->loader = $loader;
    }

    /**
     * @param string $key
     * @param array $replace
     * @param ?string $lang
     * @param bool $fallback
     * @param string[] $markup
     * @return mixed|string
     */
    public function get($key, array $replace = [], $lang = null, $fallback = true, $markup = [])
    {
        $lang = $lang ?? $this->currentLanguage;

        if (!isset($this->loaded[$lang])) {
            $this->loaded[$lang] = $this->loader->load($lang, $this->namespaces);
        }

        $line = $this->loaded[$lang][$key] ?? null;

        if ($fallback && $line === null) {
            return $this->get($key, $replace, $this->fallbackLanguage, false, $markup);
        }

        $line = $this->makeMarkupReplacements($line ?? $key, $markup);
        $line = $this->makePlaceholderReplacements($line, $replace);

        return $line;
    }


    public function choice($key, $number, array $replace = [], $locale = null)
    {
        Log::warning(self::class . '::choice() not implemented.');
        return $this->get($key, $replace, $locale);
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->currentLanguage;
    }

    /**
     * @param string $language
     * @return void
     */
    public function setLocale($language)
    {
        $language = LanguageParser::getPreferredLanguage($language, config('i18n.supported_languages'));

        if ($language !== null && $language !== $this->getLocale()) {
            $this->currentLanguage = $language;
        }
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param  string  $line
     * @param  array<string, string> $replace
     * @return string
     */
    protected function makePlaceholderReplacements(string $line, array $replace): string
    {
        if (count($replace) === 0) {
            return $line;
        }

        $replace = $this->sortReplacements($replace);

        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':' . $key, '{{' . $key . '}}'],
                [$value, $value],
                $line
            );
        }

        return $line;
    }

    /**
     * @param string $line
     * @param string[] $markup
     * @return string
     */
    protected function makeMarkupReplacements(string $line, array $markup): string
    {
        if (count($markup) === 0) {
            return $line;
        }

        foreach ($markup as $key => $tag) {
            $tag = $markup[$key];

            if (strpos($line, "</$key>") === false) {
                //
                // Single tag (e.g. <br/>)
                //
                /** @var string $line */
                $line = preg_replace("/<$key ?\/?>/", $tag, $line);
            } else {
                //
                // Open/closed tags (e.g. <b></b>)
                //
                /** @var string $openTag */
                /** @var string $closeTag */
                $openTag = preg_replace('/([^ \/>]+) ?\/?>(<\/[a-z0-9]+>)?$/', '$1>', $tag);
                $closeTag = preg_replace('/<([a-z0-9]+).*/', '</$1>', $tag);
                $line = preg_replace_array("/<\/?$key>/", [$openTag, $closeTag], $line);
            }
        }

        /** @var string $line */
        $line = preg_replace('/<\/?[0-9]+>/', '', $line); // Strips missed tags.

        return $line;
    }

    /**
     * Sort the replacements array.
     *
     * @param  array<string, string>    $replace
     * @return array<string, string>
     */
    protected function sortReplacements(array $replace)
    {
        return (new Collection($replace))->sortBy(function ($value, $key): int {
            return mb_strlen($key) * -1;
        })->all();
    }
}
