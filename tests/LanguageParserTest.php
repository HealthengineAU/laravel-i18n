<?php

namespace Healthengine\I18n\Tests;

use Healthengine\I18n\LanguageParser;

/**
 * @covers \Healthengine\I18n\LanguageParser
 */
class LanguageParserTest extends TestCase
{
    /**
     * @dataProvider parseAcceptLanguageHeaderProvider
     */
    public function testParse(string $input, array $expected): void
    {
        $actual = LanguageParser::parseAcceptLanguageHeader($input);

        self::assertEquals($expected, $actual);
    }

    public static function parseAcceptLanguageHeaderProvider(): array
    {
        return [
            'Multiple weighted types with quality values' => [
                'fr-ch, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5',
                ['fr-ch', 'fr', 'en', 'de', '*'],
            ],
            'Out of order weighted' => [
                'en-us, ko; q=0.334, fr;q=0.5',
                ['en-us', 'fr', 'ko'],
            ],
            'No weights provided retains order' => [
                'zh, en-US, zh-Hant-TW, zh-Hans-CN',
                ['zh', 'en-us', 'zh-hant-tw', 'zh-hans-cn'],
            ],
            'Values outside of 0.0-1.0 are assumed to be 1.0' => [
                'en-US, ko; q=1.5, fr;q=1.0',
                ['en-us', 'ko', 'fr'],
            ],
            'Anything' => [
                '*',
                ['*'],
            ],
            'Partial matches' => [
                'zh-*-cn, zh-hans-*, >SomethinElse',
                ['zh-*-cn', 'zh-hans-*'],
            ],
        ];
    }
}
