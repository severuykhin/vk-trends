<?php

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\VarDumper;
use common\models\Group;
use common\components\Elastic;

/**
 * Отображение страниц сайта
 * Class SiteController
 * @package api\controllers
 */
class GroupController extends Controller
{

    /**
     * Подключенные внешние экшены
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Подключенные поведения
     * @return array
     */
    public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'index' => ['get']
				],
			],
		];
    }

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }
    
    public function actionVportrait()
    {
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

    /**
     * Главная страница
     * @return string
     */
    public function actionSummary()
	{
        $id = Yii::$app->request->get('id');
        $model = Group::findOne($id);

        $elastic = new Elastic();

        $elastic->load(Yii::$app->request->get());

        $summary = $elastic->getGroupSummary([
            'vk_group_id' => $model->vk_group_id
        ]);

        return [
            'result' => 'success',
            'payload' => $summary
        ];
    }
    
    public function actionPostsPortrait()
    {
        $id = Yii::$app->request->get('id');
        $model = Group::findOne($id);

        $elastic = new Elastic();

        $elastic->load(Yii::$app->request->get());

        $comments_verbal_portrait = $elastic->getGroupPostsVerbalPortrait([
            'vk_group_id' => $model->vk_group_id
        ]);

        return [
            'result' => 'success',
            'payload' => $comments_verbal_portrait
        ];
    }

    public function actionTopCommentators()
    {
        $id = Yii::$app->request->get('id');
        $model = Group::findOne($id);

        $elastic = new Elastic();

        $elastic->load(Yii::$app->request->get());

        $top_group_commentators = $elastic->getTopСommentatorSummary([
            'vk_group_id' => $model->vk_group_id
        ]);

        return [
            'result' => 'success',
            'payload' => $top_group_commentators
        ];
    }

}
