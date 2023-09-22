<?php

namespace Healthengine\I18n;

use Illuminate\Support\Str;
use UnexpectedValueException;

/**
 * Parses Language Tags from Accept Language Header.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
 * @see https://tools.ietf.org/html/bcp47
 * @see https://en.wikipedia.org/wiki/IETF_language_tag
 */
final class LanguageParser
{
    /**
     * Parses standard Accept-Language headers.
     * e.g. "Accept-Language: fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5"
     *
     * @param string $acceptLanguageHeader
     * @return string[] A list of BCP 47 language tags.
     */
    public static function parseAcceptLanguageHeader(string $acceptLanguageHeader): array
    {
        return Str::of($acceptLanguageHeader)
            ->explode(',')
            ->mapWithKeys(fn ($entry) => self::mapLanguageEntry($entry))
            ->sortDesc()
            ->keys()
            ->all();
    }

    /**
     * Returns the best compatible language.
     *
     * @param string|string[] $preferred
     * @param string[] $allowedLanguages
     * @return string|null
     */
    public static function getPreferredLanguage($preferred, array $allowedLanguages): ?string
    {
        $preferredLanguages = is_array($preferred) ? $preferred : LanguageParser::parseAcceptLanguageHeader($preferred);

        foreach ($preferredLanguages as $preferredLanguage) {
            $language = self::getCompatibleLanguage($preferredLanguage, $allowedLanguages);

            if ($language !== null) {
                return $language;
            }
        }

        return null;
    }

    /**
     * Returns a key-value pair with Language Tag as a key, and the priority scalar as a value.
     *
     * (Or an empty array when invalid)
     *
     * @param string $languageEntry
     * @return array<string, string>
     */
    private static function mapLanguageEntry(string $languageEntry): array
    {
        $parts = Str::of($languageEntry)->explode(';')->all();
        $languageTag = Str::of($parts[0])->trim()->lower()->match('/^([a-z0-9-*]+)$/');

        if ($languageTag->length() === 0) {
            return [];
        }

        if (array_key_exists(1, $parts)) {
            $qualityRaw = Str::of($parts[1])->match('/q ?= ?(0.[0-9]{1,3}|1.0)/');
        } else {
            $qualityRaw = Str::of('');
        }

        $qualityScalar = $qualityRaw->length() > 0 ? $qualityRaw->toString() : '1.0';

        return [(string)$languageTag => $qualityScalar];
    }

    /**
     * Takes a desired BCF 47 language tag and finds the best matching language (if one exists).
     *
     * @param string $preferred
     * @param string[] $allowedLanguages
     * @return ?string
     */
    private static function getCompatibleLanguage(string $preferred, array $allowedLanguages): ?string
    {
        $preferred = Str::of($preferred)->replace('-*', '')->lower(); // Remove wildcards.

        $configPreferredLanguage = config("i18n.fallback.{$preferred}");

        if (!is_string($configPreferredLanguage) && $configPreferredLanguage !== null) {
            throw new UnexpectedValueException(
                'Did not expect preferred language config to of type: ' . gettype($configPreferredLanguage)
            );
        }

        $preferred = $configPreferredLanguage ?? $preferred;

        if (in_array($preferred, $allowedLanguages, true)) {
            return $preferred;
        }

        $requirements = Str::of($preferred)->explode('-')->all();

        foreach ($allowedLanguages as $allowedLanguage) {
            if (Str::containsAll($allowedLanguage, $requirements)) {
                return $allowedLanguage;
            }
        }

        // No matches with "en-US"? Try "en" next.
        if (count($requirements) > 1) {
            array_pop($requirements);
            return self::getCompatibleLanguage(implode('-', $requirements), $allowedLanguages);
        }

        return null;
    }
}
