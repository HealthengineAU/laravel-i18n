<?php

namespace Healthengine\I18n\Tests\Middleware;

/**
 * @covers \Healthengine\I18n\Http\Middleware\HasLanguage
 */
class HasLanguageTestCase extends MiddlewareTestCase
{
    private const BASE_GET_URI = '/language/has/get';
    private const BASE_POST_URI = '/language/has/post';

    public function testUsesDefaultLanguage(): void
    {
        config(['i18n.language' => 'en']);

        $this->get(self::BASE_GET_URI . '?name=David', self::DEFAULT_HEADERS)->assertExactJson([
            'lang' => 'en',
            'value' => 'Hello, David'
        ]);
    }

    public function testReturnsFallbackLanguageForStrings(): void
    {
        $this->get(self::BASE_GET_URI . '?name=David&lang=fr&key=greeting.substitution.symphonyStyle', self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Hello, David'
            ]);
    }

    public function testUsesCookies(): void
    {
        $this->defaultCookies = [
            'lang' => 'fr'
        ];

        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha'
        ], self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }

    public function testTakesLangBodyParam(): void
    {
        $this->post(self::BASE_POST_URI, [
                'key' => 'greeting.substitution.i18nNextStyle',
                'lang' => 'fr',
                'name' => 'Samantha'
            ], self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }

    public function testAllowsMixtureOfBodyAndQuery(): void
    {
        $this->post(self::BASE_POST_URI . '?lang=fr', [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha'
        ], self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }

    public function testShouldNotTakeLanguageFromAcceptLanguageHeader(): void
    {
        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha'
        ], [
            'Accept-Language' => 'fr',
        ])
            ->assertExactJson([
                'lang' => 'en',
                'value' => 'Hello, Samantha'
            ]);
    }
}
