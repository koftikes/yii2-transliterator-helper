<?php

namespace sbs\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

/**
 * Class SlugInput
 * Set up $sourceModel and $sourceAttribute or just $sourceName with field name which is the source of slug field.
 */
class SlugInput extends Widget
{
    /**
     * @var Model the data model that this widget is associated with
     */
    public $model;

    /**
     * @var string the model attribute that this widget is associated with
     */
    public $attribute;

    /**
     * @var string the input name. This must be set if [[model]] and [[attribute]] are not set.
     */
    public $name;

    /**
     * @var string the selected value
     */
    public $value;

    /**
     * @var array the HTML attribute options for the input tag
     *
     * @see yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var Model The model with source field. By default will use current.
     */
    public $sourceModel;

    /**
     * @var string the attribute of input for transliteration from source model
     */
    public $sourceAttribute;

    /**
     * @var string The name of source field. Can be in form input field 'News[name]' or  input id 'news-name'.
     */
    public $sourceName;

    /**
     * @var string route to process transliterations
     */
    private $updateUrl;

    /**
     * @var string input id to take source for transliterations
     */
    private $sourceId;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        foreach (Yii::$app->controllerMap as $route => $controller) {
            if ('sbs\controllers\TransliterationController' === $controller['class']) {
                $this->updateUrl = Yii::$app->urlManager->createUrl($route . '/process');
            }
        }

        if (!$this->updateUrl) {
            throw new InvalidConfigException('You need configure you "controllerMap" section!');
        }

        if (null === $this->sourceModel) {
            $this->sourceModel = $this->model;
        }

        if (!isset($this->sourceName) && !isset($this->sourceModel, $this->sourceAttribute)) {
            throw new InvalidConfigException('Set up source field params for slug field!');
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel()
                ? Html::getInputId($this->model, $this->attribute)
                : $this->getId();
        }

        $this->sourceId = $this->sourceName
            ? \mb_strtolower(\str_replace(['[]', '][', '[', ']', ' '], ['', '-', '-', '', '-'], $this->sourceName))
            : Html::getInputId($this->sourceModel, $this->sourceAttribute);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerClientScripts();

        if ($this->hasModel()) {
            Html::addCssClass($this->options, 'form-control');

            return Html::activeTextInput($this->model, $this->attribute, $this->options);
        }

        return Html::textInput($this->name, $this->value, $this->options);
    }

    /**
     * @return bool whether this widget is associated with a data model
     */
    protected function hasModel()
    {
        return $this->model instanceof Model && null !== $this->attribute;
    }

    /**
     * Register widget asset.
     */
    protected function registerClientScripts()
    {
        $this->getView()->registerJs((string) \preg_replace(['/\s+\n/', '/\n\s+/', '/ +/'], ['', '', ' '], "      
            $(function () {
                var timer,
                    sourceField = $('#{$this->sourceId}'),
                    targetField = $('#{$this->options['id']}'),
                    updateUrl = '{$this->updateUrl}',
                    editable = targetField.val().length === 0,
                    value = sourceField.val();
                if (targetField.val().length !== 0) {
                    $.get(updateUrl, {data: sourceField.val()}, function (r) {
                        editable = targetField.val() === r;
                    });
                }
                sourceField.on('keyup blur copy paste cut start', function () {
                    clearTimeout(timer);
                    if (editable && value != sourceField.val()) {
                        timer = setTimeout(function () {
                            value = sourceField.val();
                            targetField.attr('disabled', 'disabled');
                            $.get(updateUrl, {data: sourceField.val()}, function (r) {
                                targetField.val(r).removeAttr('disabled');
                            });
                        }, 300);
                    }
                });
                targetField.on('change', function () {
                    editable = $(this).val().length == 0;
                });
            });
    "), View::POS_END, $this->options['id']);
    }
}
