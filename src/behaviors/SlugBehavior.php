<?php

namespace sbs\behaviors;

use sbs\helpers\TransliteratorHelper;
use yii\behaviors\SluggableBehavior;

class SlugBehavior extends SluggableBehavior
{
    /**
     * @var bool setting provides transliteration to lower case
     */
    public $lowercase = true;

    /**
     * This method is called by [[getValue]] to generate the slug.
     *
     * @param array $slugParts an array of strings that should be concatenated and converted to generate the slug value
     *
     * @return string the conversion result
     */
    protected function generateSlug($slugParts)
    {
        $string = \str_replace(' ', '-', \implode('-', $slugParts));
        $string = TransliteratorHelper::process($string);

        return $this->lowercase ? \mb_strtolower($string) : $string;
    }
}
