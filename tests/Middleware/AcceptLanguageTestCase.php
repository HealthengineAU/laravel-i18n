<?php

namespace Healthengine\I18n\Tests\Middleware;

/**
 * @covers \Healthengine\I18n\Http\Middleware\AcceptLanguage
 */
class AcceptLanguageTestCase extends MiddlewareTestCase
{
    private const BASE_GET_URI = '/language/accept/get';
    private const BASE_POST_URI = '/language/accept/post';

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        // Default language is French for these tests.
        // English will fallback to French.
        $app['config']['i18n.language'] = 'fr';
    }

    public function testUsesDefaultLanguage(): void
    {
        $this->get(self::BASE_GET_URI . '?name=David&key=greeting.substitution.i18nNextStyle', self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, David'
            ]);
    }

    public function testIgnoresQueryParam(): void
    {
        $this->get(self::BASE_GET_URI . '?name=David&lang=en&key=greeting.substitution.i18nNextStyle', self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, David'
            ]);
    }

    public function testIgnoresBodyParam(): void
    {
        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha',
            'lang' => 'en',
        ], self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }

    public function testIgnoresBothBodyAndQuery(): void
    {
        $this->post(self::BASE_POST_URI . '?lang=en', [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha'
        ], self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }

    public function testIgnoresCookies(): void
    {
        $this->defaultCookies = [
            'lang' => 'en'
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

    public function testShouldOnlyTakeLanguageFromAcceptLanguageHeader(): void
    {
        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha'
        ], ['Accept-Language' => 'en'])
            ->assertExactJson([
                'lang' => 'en',
                'value' => 'Hello, Samantha'
            ]);
    }
}
