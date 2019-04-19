<?php

namespace sbs\behaviors;

use yii\behaviors\SluggableBehavior;
use sbs\helpers\TransliteratorHelper;

class SlugBehavior extends SluggableBehavior
{
    /**
     * @var bool setting provides transliteration to lower case.
     */
    public $lowercase = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * This method is called by [[getValue]] to generate the slug.
     * @param array $slugParts an array of strings that should be concatenated and converted to generate the slug value.
     * @return string the conversion result.
     */
    protected function generateSlug($slugParts)
    {
        $string = str_replace(' ', '-', implode('-', $slugParts));
        $string = TransliteratorHelper::process($string);

        return $this->lowercase ? strtolower($string) : $string;
    }
}
