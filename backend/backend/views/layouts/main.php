<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <?php
    NavBar::begin([
        'brandLabel' => 'template',
        'brandUrl' => '/',
        'innerContainerOptions' => [
	        'class' => 'container-fluid'
        ],
        'options' => [
            'class' => 'navbar navbar-default navbar-static-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
                'label' => 'Пользователи',
                'url' => ['/user/index'],
                'visible' => Yii::$app->user->can('admin')
            ],
            [
                'label' => 'Выход',
                'url' => ['/site/logout'],
                'visible' => !Yii::$app->user->isGuest
            ],
            [
                'label' => 'Языки',
                'items' => [
                    [
                        'label' => 'Русский',
                        'url' => Yii::$app->request->url
                    ],
                    [
                        'label' => 'Английский',
                        'url' => '/en' . Yii::$app->request->url
                    ]
                ]
            ]
        ],
    ]);
    NavBar::end();
    ?>
    <div class="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    echo Breadcrumbs::widget([
                        'homeLink' => [
                            'label' => 'Мои виджеты',
                            'url' => ['/site/index']
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);
                    $flashes = Yii::$app->session->allFlashes;
                    foreach ($flashes as $type => $flash) {
                        echo Alert::widget([
                            'options' => [
                                'class' => 'alert-' . $type,
                            ],
                            'body' => $flash
                        ]);
                    }
                    ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
