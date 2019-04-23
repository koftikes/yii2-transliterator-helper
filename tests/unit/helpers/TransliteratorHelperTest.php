<?php

namespace tests\unit\helpers;

use PHPUnit\Framework\TestCase;
use sbs\helpers\TransliteratorHelper;

/**
 * Class TransliteratorHelperTest.
 */
class TransliteratorHelperTest extends TestCase
{
    public function testProcess()
    {
        self::assertEquals(
            'AAAAAAAECEEEEIIIIDNOOOOOUUUUYssaaaaaaaeceeeeiiiidnooooouuuuyy',
            TransliteratorHelper::process('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ', 'en')
        );
        // German
        self::assertEquals('AeaeOeoeUeuess', TransliteratorHelper::process('ÄäÖöÜüẞß', 'de'));
        // Danish
        self::assertEquals('AEaeOeoeAaaa', TransliteratorHelper::process('ÆæØøÅå', 'da'));
        // Spanish
        self::assertEquals('AaEeIiOoUu', TransliteratorHelper::process('ÁáÉéÍíÓóÚú', 'es'));
        // Cyrillic
        self::assertEquals('GDZhZYYgdzhzyy', TransliteratorHelper::process('ГДЖЗЫЙгджзый', 'en'));
    }
}
