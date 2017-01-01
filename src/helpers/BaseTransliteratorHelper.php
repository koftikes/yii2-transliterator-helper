<?php

namespace sbs\helpers;

use Yii;

/**
 * BaseTransliteratorHelper provides concrete implementation for [[TransliteratorHelper]].
 * Do not use BaseTransliteratorHelper. Use [[TransliteratorHelper]] instead.
 */
class BaseTransliteratorHelper
{
    const UNKNOWN = '';

    /**
     * Transliterates UTF-8 encoded text to US-ASCII.
     *
     * @param string $string the UTF-8 encoded string.
     * @param string $language optional ISO 639 language code that denotes the language of the input and
     * is used to apply language-specific variations. Otherwise the current display language will be used.
     * @return string the transliterated text
     */
    public static function process($string, $language = null)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        static $tail_bytes;

        if (!isset($tail_bytes)) {
            $tail_bytes = [];
            for ($n = 0; $n < 256; $n++) {
                if ($n < 0xc0) {
                    $remaining = 0;
                } elseif ($n < 0xe0) {
                    $remaining = 1;
                } elseif ($n < 0xf0) {
                    $remaining = 2;
                } elseif ($n < 0xf8) {
                    $remaining = 3;
                } elseif ($n < 0xfc) {
                    $remaining = 4;
                } elseif ($n < 0xfe) {
                    $remaining = 5;
                } else {
                    $remaining = 0;
                }
                $tail_bytes[chr($n)] = $remaining;
            }
        }

        preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);

        $result = [];
        foreach ($matches[0] as $str) {
            if ($str[0] < "\x80") {
                $result[] = $str;
                continue;
            }

            $head = '';
            $chunk = strlen($str);
            $len = $chunk + 1;
            for ($i = -1; --$len;) {
                $c = $str[++$i];
                if ($remaining = $tail_bytes[$c]) {
                    $sequence = $head = $c;
                    do {
                        if (--$len && ($c = $str[++$i]) >= "\x80" && $c < "\xc0") {
                            $sequence .= $c;
                        } else {
                            if ($len == 0) {
                                $result[] = self::UNKNOWN;
                                break 2;
                            } else {
                                $result[] = self::UNKNOWN;
                                --$i;
                                ++$len;
                                continue 2;
                            }
                        }
                    } while (--$remaining);

                    $n = ord($head);
                    $ord = 0;
                    if ($n <= 0xdf) {
                        $ord = ($n - 192) * 64 + (ord($sequence[1]) - 128);
                    } elseif ($n <= 0xef) {
                        $ord = ($n - 224) * 4096 + (ord($sequence[1]) - 128) * 64 + (ord($sequence[2]) - 128);
                    } elseif ($n <= 0xf7) {
                        $ord = ($n - 240) * 262144 + (ord($sequence[1]) - 128) * 4096 +
                            (ord($sequence[2]) - 128) * 64 + (ord($sequence[3]) - 128);
                    } elseif ($n <= 0xfb) {
                        $ord = ($n - 248) * 16777216 + (ord($sequence[1]) - 128) * 262144 +
                            (ord($sequence[2]) - 128) * 4096 + (ord($sequence[3]) - 128) * 64 + (ord($sequence[4]) - 128);
                    } elseif ($n <= 0xfd) {
                        $ord = ($n - 252) * 1073741824 + (ord($sequence[1]) - 128) * 16777216 +
                            (ord($sequence[2]) - 128) * 262144 + (ord($sequence[3]) - 128) * 4096 +
                            (ord($sequence[4]) - 128) * 64 + (ord($sequence[5]) - 128);
                    }
                    $result[] = static::replace($ord, $language);
                    $head = '';
                } elseif ($c < "\x80") {
                    $result[] = $c;
                    $head = '';
                } elseif ($c < "\xc0") {
                    if ($head == '') {
                        $result[] = self::UNKNOWN;
                    }
                } else {
                    $result[] = self::UNKNOWN;
                    $head = '';
                }
            }
        }

        $string = implode('', $result);
        $string = preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '', $string);
        $string = preg_replace('/[=\s—–-]+/u', '-', $string);

        return trim($string, '-');
    }

    /**
     * @param int $ord an ordinal Unicode character code
     * @param string $language optional ISO 639 language code that specifies the language of the input and is used to apply
     * @return string the ASCII replacement character
     */
    public static function replace($ord, $language = null)
    {
        static $map = [];

        if (!isset($language)) {
            $language = Yii::$app->language;
            if (strpos($language, '-')) {
                $language = substr($language, 0, strpos($language, '-'));
            }
        }

        $key = $ord >> 8;

        if (!isset($map[$key][$language])) {
            $base = [];
            $data_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
            $file = realpath($data_dir . sprintf('x%02x', $key) . '.php');
            if (file_exists($file)) {
                include $file;
                // $base + $variant are included vars from
                if (isset($base)) {
                    if ($language != 'en' && isset($variant[$language])) {
                        $map[$key][$language] = $variant[$language] + $base;
                    } else {
                        $map[$key][$language] = $base;
                    }
                }
            } else {
                $map[$key][$language] = [];
            }
        }

        $ord = $ord & 255;

        return isset($map[$key][$language][$ord]) ? $map[$key][$language][$ord] : self::UNKNOWN;
    }
}
