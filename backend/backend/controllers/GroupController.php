<?php

namespace backend\controllers;

use Yii;
use common\models\Group;
use common\workers\GroupProcess;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\Elastic;
use yii\helpers\VarDumper as Dump;

/**
 * GroupController implements the CRUD actions for Group model.
 */
class GroupController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Group models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Group::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Group model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        $elastic = new Elastic();

        $comments_verbal_portrait = $elastic->getGroupCommentsVerbalPortrait([
            'vk_group_id' => $model->vk_group_id
        ]);

        $summary = $elastic->getGroupSummary([
            'vk_group_id' => $model->vk_group_id
        ]);

        return $this->render('view', [
            'model' => $model,
            'data' => [
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Creates a new Group model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Group();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $data = Yii::$app->request->post('Group');
            if (isset($data['category_ids'])) {
                $model->saveIds($data['category_ids']);
            } else {
                $model->saveIds([]);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Group model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $data = Yii::$app->request->post('Group');
            if (isset($data['category_ids'])) {
                $model->saveIds($data['category_ids']);
            } else {
                $model->saveIds([]);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Group model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionProcess($id) 
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $groupProcess = new GroupProcess();
            $res = $groupProcess->run($id);

            return [
                'result' => 'success',
                'payload' => json_decode($res, true),
                'errors' => []
            ];
        } catch (Throwable $e) {
            return [
                'result' => 'error',
                'payload' => null,
                'errors' => [
                    $e->getMessage()
                ]
            ];
        }
    }
    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Group the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
