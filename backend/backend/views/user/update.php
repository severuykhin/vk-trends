<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = 'Редактировать: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';

?>

<div>
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(); ?>
	<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'status')->dropDownList(['не активирован', 'активен', 'забанен', 'удален']) ?>
	<?= $form->field($model, 'role')->dropDownList(['user' => 'user', 'admin' => 'admin']) ?>
    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
	<div class="form-group">
		<?= Html::submitButton('Редактировать', ['class' => 'btn btn-primary']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>