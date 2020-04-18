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
class ApiController extends Controller
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
    public function actionGroup()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->get('id');
        $model = Group::findOne($id);

        $elastic = new Elastic();

        $elastic->load(Yii::$app->request->get());

        $comments_verbal_portrait = $elastic->getGroupCommentsVerbalPortrait([
            'vk_group_id' => $model->vk_group_id
        ]);

        return [
            'result' => 'success',
            'payload' => $comments_verbal_portrait
        ];
    }
}