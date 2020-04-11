<?php

namespace common\components;

use Yii;
use yii\helpers\VarDumper;
use yii\web\Request;

class LangRequest extends Request
{
    protected function resolveRequestUri()
    {
        $lang_prefix = null;
        $requestUri = parent::resolveRequestUri();
        $requestUriToList = explode('/', $requestUri);
        $lang_url = isset($requestUriToList[1]) ? $requestUriToList[1] : null;
        if (
            ($lang_url !== null)
            && (isset(Yii::$app->params['language'][$lang_url]))
            && (strpos($requestUri, $lang_url) === 1)
        )
        {
            $requestUri = substr($requestUri, strlen($lang_url) + 1 );
        } else {
            $lang_url = Yii::$app->params['defaultLanguage'];
        }
        Yii::$app->language = $lang_url;
        Yii::$app->sourceLanguage = Yii::$app->params['language'][$lang_url];
        Yii::$app->formatter->locale = Yii::$app->params['language'][$lang_url];
        return $requestUri;
    }
}