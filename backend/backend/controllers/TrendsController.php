<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\components\Elastic;
use common\models\Group;


/**
 * Управление категориями
 * @package backend\controllers
 */
class TrendsController extends Controller
{
    

    /**
     * Подключенные поведения
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'move' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        
    }

    /**
     * Все категории
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}