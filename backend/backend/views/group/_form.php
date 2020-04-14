<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\models\Category;
use \common\models\City;

/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $form yii\widgets\ActiveForm */

$model->setCategoryIds();

?>

<div class="group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vk_group_id')->textInput() ?>

    <?= $form->field($model, 'city_id')->dropDownList(City::getIds()) ?>

    <?= $form->field($model, 'category_ids')->dropDownList(Category::getIds(), [
        'multiple' => true
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
