Transliterator Helper for Yii 2
===============================

[![Build Status](https://travis-ci.com/koftikes/yii2-transliterator-helper.svg?branch=master)](https://travis-ci.com/koftikes/yii2-transliterator-helper)

Transliterator Helper transliterates UTF-8 encoded text to US-ASCII.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
composer require sbs/yii2-transliterator-helper
```

or add

```json
"sbs/yii2-transliterator-helper": "*"
```

to the require section of your application's `composer.json` file.

Usage
-----
Pass to the method `process()` the UTF-8 encoded string you wish to transliterate:

```
use sbs\helpers\TransliteratorHelper;

// will echo AAAAAAAECEEEEIIIIDNOOOOOUUUUYssaaaaaaaeceeeeiiiidnooooouuuuyy
TransliteratorHelper::process('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ', 'en'));
```

You can use it as application behavior:

```
use sbs\behaviors\SlugBehavior;

//...
public function behaviors()
{
    return [
        //...
        [
            'class' => SlugBehavior::class,
            'attribute' => 'title',
            'slugAttribute' => 'slug',
        ],
    ];
}
```

#### Since version 0.3 you can use SlugInput widget:

**Configurations:**

You need a registration controller in your main config file in section  `controllerMap`:

```php
use sbs\controllers\TransliterationController;

//...
'controllerMap' => [
    'transliteration' => [
        'class' => TransliterationController::class,
        'lowercase' => false //provides transliteration to lower case, true by default.
    ]
],
//...
```

**Like a widget:**

```php
use sbs\widgets\SlugInput;

echo SlugInput::widget([
    'name' => 'News[slug]',
    'sourceName' => 'News[title]'
]);
```

**Like an ActiveForm widget:**

```php
use sbs\widgets\SlugInput;

echo $form->field($model, 'slug')->widget(SlugInput::class, [
    'sourceAttribute' => 'title'
]);

```

That's all. Enjoy.
