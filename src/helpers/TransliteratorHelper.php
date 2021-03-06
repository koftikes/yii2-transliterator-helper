<?php

namespace sbs\helpers;

/**
 * TransliteratorHelper transliterates UTF-8 encoded text to US-ASCII. Based on Drupal's transliteration module, which
 * is based on UtfNormal.php from MediaWiki (http://www.mediawiki.org).
 *
 * @see http://userguide.icu-project.org/transforms/general
 * @see http://www.utf8-chartable.de/unicode-utf8-table.pl
 * @see https://doc.wikimedia.org/mediawiki-core/master/php/html/classUtfNormal.html
 * @see http://drupal.org/project/issues/transliteration
 */
class TransliteratorHelper extends BaseTransliteratorHelper
{
}
