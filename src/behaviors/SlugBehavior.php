<?php

namespace sbs\behaviors;

use yii\behaviors\SluggableBehavior;
use sbs\helpers\TransliteratorHelper;

class SlugBehavior extends SluggableBehavior
{
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
        $string = TransliteratorHelper::process(implode('-', $slugParts));

        return $this->lowercase ? strtolower($string) : $string;
    }
}
