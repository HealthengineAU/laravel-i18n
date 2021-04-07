<?php

namespace HealthEngine\I18n\Tests;

class HelperTest extends TestCase
{
    public function testSubstitutesVariables(): void
    {
        $expected = 'Hello, Mark';

        // Substitute :variable style.
        $actual1 = i18n('greeting.substitution.symphonyStyle', ['name' => 'Mark']);
        self::assertEquals($expected, $actual1);

        // Substitute {{variable}} style.
        $actual2 = i18n('greeting.substitution.i18nNextStyle', ['name' => 'Mark']);
        self::assertEquals($expected, $actual2);
    }

    public function testSubstitutesMarkup(): void
    {
        $actual = i18n('greeting.withMarkup', ['name' => 'Sandra'], [
            '<a href="#test_url">', '</a>',
        ]);

        self::assertEquals('Hi, Sandra. Click <a href="#test_url">this link</a> please.', $actual);
    }

    public function testSubstitutesMarkupWhenNoVars(): void
    {
        $actual = i18n('greeting.withOnlyMarkup', [], [
            '<a href="https://fake.url/">', '</a>',
        ]);

        self::assertEquals('Hi, there! <a href="https://fake.url/">Click this link?</a>', $actual);
    }

    public function testUsesCurrentLanguageWhenSet(): void
    {
        app()->setLocale('fr');

        $actual = i18n('greeting.withMarkup', ['name' => 'Cecile'], [
            '<a href="#test_url">', '</a>',
        ]);

        self::assertEquals('Bonjour Cecile. Cliquez sur <a href="#test_url">ce lien</a> s\'il vous plaît.', $actual);
    }
}
