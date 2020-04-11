<?php

namespace backend\controllers;

use backend\models\form\UserUpdate;
use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Управление пользователями
 * @package backend\controllers
 */
class UserController extends Controller
{

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
	                'active' => ['post'],
	                'ban' => ['post'],
	                'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Все пользователи системы
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'pagination' => [
	            'pageSize' => 100,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Обновление пользователя
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->password) {
                $model->setPassword($model->password);
            }
            $model->save();
            return $this->redirect(['view', 'id' => $id]);
        }
        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Просмотр данных пользователя в админке
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Активация пользователя
     * @param $id
     * @return \yii\web\Response
     */
    public function actionActive($id)
	{
		$model = $this->findModel($id)->active();
		return $this->redirect(['view', 'id' => $id]);
	}

    /**
     * Бан пользователя
     * @param $id
     * @return \yii\web\Response
     */
    public function actionBan($id)
	{
		$model = $this->findModel($id)->ban();
		return $this->redirect(['view', 'id' => $id]);
	}

    /**
     * Удаление пользователя
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
	    return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Поиск конкретного пользователя по ее ключу, если пользователь не найден, то будет выведена 404 ошибка
     * @param integer $id ключ баннера
     * @return модель баннера
     * @throws NotFoundHttpException 404 если модель не найдена
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
