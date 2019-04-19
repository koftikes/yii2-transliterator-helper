<?php

namespace sbs\controllers;

use Yii;
use yii\web\Controller;
use sbs\helpers\TransliteratorHelper;

/**
 * Class TransliterationController
 * @package sbs\controllers
 */
class TransliterationController extends Controller
{
    public $lowercase = true;

    public function actionProcess()
    {
        $data = Yii::$app->request->get('data') ?: Yii::$app->request->post('data');
        $data = TransliteratorHelper::process($data);
        echo $this->lowercase ? strtolower($data) : $data;
    }
}
