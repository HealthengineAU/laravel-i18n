<?php

namespace HealthEngine\I18n\Tests\Middleware;

class DetectLanguageTest extends MiddlewareTest
{
    private const BASE_GET_URI = '/language/detect/get';
    private const BASE_POST_URI = '/language/detect/post';

    public function testUsesDefaultLanguage(): void
    {
        $this->get(self::BASE_GET_URI . '?name=David&key=greeting.substitution.i18nNextStyle', self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'en',
                'value' => 'Hello, David'
            ]);
    }

    public function testUsesQueryParam(): void
    {
        $this->get(self::BASE_GET_URI . '?name=David&lang=fr&key=greeting.substitution.i18nNextStyle', self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, David'
            ]);
    }

    public function testUsesBodyParam(): void
    {
        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha',
            'lang' => 'fr',
        ], self::DEFAULT_HEADERS)
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
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

    public function testPrefersParamsOverHeaders(): void
    {
        $this->post(self::BASE_POST_URI . '?lang=fr', [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha',
        ], ['Accept-Language' => 'en'])
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }

    public function testPrefersHeadersOverCookies(): void
    {
        $this->defaultCookies = [
            'lang' => 'fr',
        ];

        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha',
        ], ['Accept-Language' => 'fr'])
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }

    public function testPrefersParamsOverCookies(): void
    {
        $this->defaultCookies = [
            'lang' => 'fr',
        ];

        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha',
            'lang' => 'de',
        ])
            ->assertExactJson([
                'lang' => 'de',
                'value' => 'Hello, Samantha'
            ]);
    }

    public function testAcceptLanguageHeader(): void
    {
        $this->post(self::BASE_POST_URI, [
            'key' => 'greeting.substitution.i18nNextStyle',
            'name' => 'Samantha'
        ], ['Accept-Language' => 'fr'])
            ->assertExactJson([
                'lang' => 'fr',
                'value' => 'Bonjour, Samantha'
            ]);
    }
}
