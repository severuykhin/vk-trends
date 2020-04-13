<?php

use yii\helpers\Html;
use bupy7\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-index">

    <h3>
        <?= Html::encode($this->title) ?>
        <?= Html::a('Create Group', ['create'], ['class' => 'btn btn-success']) ?>
    </h3>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'vk_group_id',
            ['class' => 'yii\grid\ActionColumn'],
            [
                'header'=>'Действия', 
                'headerOptions' => ['width' => '80'],
                'content' => function ($model) {
                    return '<button type="button" data-role="group-index-btn" data-id="'. $model->vk_group_id .'" class="btn btn-success"><span class="glyphicon glyphicon-play"></span></button>';
                }
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
