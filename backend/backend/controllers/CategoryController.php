<?php

namespace backend\controllers;

use Yii;
use common\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
// use vova07\imperavi\actions\GetAction;
// use vova07\imperavi\actions\UploadAction;

/**
 * Управление категориями
 * @package backend\controllers
 */
class CategoryController extends Controller
{
    

    /**
     * Подключенные поведения
     * @return array
     */
    public function behaviors()
    {
        return [
            // 'access' => [
            //     'class' => AccessControl::class,
            //     'rules' => [
            //         [
            //             'allow' => true,
            //             'roles' => ['admin'],
            //         ],
            //     ],
            // ],
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
    public function actionIndex($id = 1)
    {
        $model = $this->findModel($id);
        $query = $model->children(1);
        $parents = $model->parents()->all();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $this->render('index', [
            'model' => $model,
            'parents' => $parents,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate($id = 1)
    {
        $model = new Category();
        $model->parent_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $root = Category::findOne($model->parent_id);
            $model->appendTo($root);
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Обновление категории
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        if ($id == 1) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = $this->findModel($id);
        $parents = $model->parents()->all();
        $root = end($parents);
        $model->parent_id = $root->id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($root->id == $model->parent_id) {
                $model->save();
            } else {
                $root = Category::findOne($model->parent_id);
                $model->appendTo($root);
            }
            return $this->redirect(['view', 'id' => $id]);
        }
        return $this->render('update', [
            'model' => $model,
            'parents' => $parents
        ]);
    }

    /**
     * Просмотр данных категории
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        if ($id == 1) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'parents' => $model->parents()->all()
        ]);
    }

    /**
     * @param $type
     * @param $id
     */
    public function actionMove($type, $id)
    {
        $model = Category::findOne($id);
        if ($type == 'up' and ($root = $model->prev()->one())) {
            $model->insertBefore($root);
        }
        if ($type == 'down' and ($root = $model->next()->one())) {
            $model->insertAfter($root);
        }
        $this->redirect(['index', 'id' => $model->parents(1)->one()->id]);
    }

    /**
     * Удаление категории
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Поиск конкретной категории по ее ключу
     * @param integer $id ключ баннера
     * @return модель баннера
     * @throws NotFoundHttpException 404 если модель не найдена
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}