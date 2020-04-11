<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('common', 'Login');

?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
		        <p>
                    <?= Yii::t('common', 'password-reset') . ' ' . Html::a(Yii::t('common', 'reset-it'), ['site/request-password-reset']) ?>.
		        </p>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('common', 'Login'), ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
