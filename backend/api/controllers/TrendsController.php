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
class TrendsController extends Controller
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
    
    public function actionSearch()
    {

        /**
         * + Запрос - ключевое слово по ключевым словам
         * + запрос - фраза по тексту
         * + Дата начала 
         * + Дата конца
         * + Шаг отображения - день, час, месяц, неделя
         * + Точность - от 0 до 100% - через slop
         * Города, Города - по названию
         * Гео позиция по координатам
         * + Категория
         * + Тип - комментарии, посты, обсуждения - вариации, по отдельности и все вместе
         */

        $query = Yii::$app->request->get('query');

        $elastic = new Elastic();

        $elastic->load(Yii::$app->request->get());

        $query_data_set = $elastic->searchReferences([
            'query' => $query
        ]);

        return [
            'result' => 'success',
            'prev_query' => Yii::$app->request->get(),
            'payload' => $query_data_set
        ];
    }
}
