<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\models\Category;

$categories = Category::find()->where(['<>', 'id', '1'])->indexBy('id')->all();

?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 300, 'placeholder' => 'название категории']) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => 300, 'placeholder' => 'slug']) ?>

    <?= $form->field($model, 'parent_id')->dropDownList($model->CategoryArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>