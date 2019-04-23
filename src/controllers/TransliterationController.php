<?php

namespace sbs\controllers;

use sbs\helpers\TransliteratorHelper;
use Yii;
use yii\web\Controller;

/**
 * Class TransliterationController.
 */
class TransliterationController extends Controller
{
    public $lowercase = true;

    public function actionProcess()
    {
        $data = Yii::$app->request->get('data') ?: Yii::$app->request->post('data');
        $data = TransliteratorHelper::process($data);
        echo $this->lowercase ? mb_strtolower($data) : $data;
    }
}
