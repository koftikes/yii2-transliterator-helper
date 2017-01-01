Transliterator Helper for Yii 2
===============================

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
TransliteratorHelper::process('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ', '', 'en'));
```
