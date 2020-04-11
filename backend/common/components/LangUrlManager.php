<?php

namespace common\components;

use Yii;
use yii\web\UrlManager;

class LangUrlManager extends UrlManager
{
    public function createUrl($params)
    {
        $url = parent::createUrl($params);

        if (Yii::$app->language == Yii::$app->params['defaultLanguage']) {
            return $url;
        } else {
            return '/' . Yii::$app->language . $url;
        }
    }
}