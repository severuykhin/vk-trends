<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\City */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lngc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ltdc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lng1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ltd1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lng2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ltd2')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
