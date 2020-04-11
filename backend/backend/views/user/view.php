<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Активировать', ['active', 'id' => $model->id], [
	        'class' => 'btn btn-success',
	        'data' => [
		        'confirm' => 'Вы уверены, что хотите активировать этого пользователя?',
		        'method' => 'post',
	        ],
        ]) ?>
        <?= Html::a('Забанить', ['ban', 'id' => $model->id], [
	        'class' => 'btn btn-warning',
	        'data' => [
		        'confirm' => 'Вы уверены, что хотите забанить этого пользователя?',
		        'method' => 'post',
	        ],
        ]) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            'email:email',
            'statusName',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
