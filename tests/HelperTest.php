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

    public function testUsesCurrentLanguageWhenSet(): void
    {
        app()->setLocale('fr');

        $actual = i18n('greeting.withMarkup', ['name' => 'Cecile'], [
            '<a href="#test_url">', '</a>',
        ]);

        self::assertEquals('Bonjour Cecile. Cliquez sur <a href="#test_url">ce lien</a> s\'il vous plaît.', $actual);
    }

    /**
     * @dataProvider markupSubstitutionProvider
     * @param string $string
     * @param array<string, string> $replace
     * @param string[] $markup
     * @param string $expected
     */
    public function testSubstitutesMarkup(string $string, array $replace, array $markup, string $expected): void
    {
        $actual = i18n($string, $replace, $markup);

        self::assertEquals($expected, $actual);
    }

    public function markupSubstitutionProvider(): array
    {
        return [
            'Inserts at correct index' => [
                'Hi! <1>Click this</1> <0>link?</0>',
                [],
                [
                    '<a href="https://first.url/">',
                    '<a href="https://second.url/" target="_blank" />'
                ],
                'Hi! <a href="https://second.url/" target="_blank">Click this</a> <a href="https://first.url/">link?</a>'
            ],
            'Inserts at explicit indices' => [
                'Hi! <1>Click this</1> <0>link?</0>',
                [],
                [
                    1 => '<a href="https://first.url/"/>',
                    0 => '<a href="https://second.url/" />'
                ],
                'Hi! <a href="https://first.url/">Click this</a> <a href="https://second.url/">link?</a>'
            ],
            'Accepts double tag in the markup array' => [
                'Hello <0>link</0>',
                [],
                ['<a href="#link"></a>'],
                'Hello <a href="#link">link</a>'
            ],
            'Accepts single tag pattern 1' => [
                'Hello <0>link</0>',
                [],
                ['<a href="#link" />'],
                'Hello <a href="#link">link</a>'
            ],
            'Accepts single tag pattern 2' => [
                'Hello <0>link</0>',
                [],
                ['<a href="#link" target="_blank" aria-label="example" />'],
                'Hello <a href="#link" target="_blank" aria-label="example">link</a>'
            ],
            'Accepts single tag pattern 3' => [
                'Hello <0>link</0>',
                [],
                ['<a href="#link">'],
                'Hello <a href="#link">link</a>'
            ],
            'Accepts nested tags' => [
                'Hello <0><1>link</1></0>',
                [],
                ['<h1>', '<b>'],
                'Hello <h1><b>link</b></h1>'
            ],
            'Works with variables' => [
                'Hello <0><1>{{name}}</1></0>',
                ['name' => 'Patricia'],
                ['<h1>', '<b>'],
                'Hello <h1><b>Patricia</b></h1>'
            ],
            'Works with variables that have markup inside of them' => [
                'What is <0><1>{{this}}</1></0>',
                ['this' => '<a href="#example">Open me</a>'],
                ['<h1>', '<b>'],
                'What is <h1><b><a href="#example">Open me</a></b></h1>'
            ],
            'Solo tag is spat out exactly as it is put in' => [
                'What is <0> trying <1>',
                [],
                [
                    '<button name="submit" type="submit">',
                    '<br/>'
                ],
                'What is <button name="submit" type="submit"> trying <br/>'
            ],
            'Strips missed tags' => [
                '<0>Hey</0>, there is <1>missed tags</1> here.',
                [],
                ['<b></b>'],
                '<b>Hey</b>, there is missed tags here.'
            ],
            'Handles RTL languages' => [
                '<0>سجّلْ الدخول<0/> بهدف حجزٍ أسرع عن طريق تفاصيلك المحفوظة',
                [],
                ['<a href="#a">'],
                '<a href="#a">سجّلْ الدخول</a> بهدف حجزٍ أسرع عن طريق تفاصيلك المحفوظة',
            ]
        ];
    }

    public function testUsesAppropriateContentDirection(): void
    {
        app()->setLocale('fr');
        self::assertEquals('ltr', i18n_dir());

        app()->setLocale('ar');
        self::assertEquals('rtl', i18n_dir());
    }
}
