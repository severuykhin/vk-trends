<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model common\models\Group */

$this->title = "Тренды";
$this->params['breadcrumbs'][] = ['label' => 'Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div id="root"></div>


<?php

$script = <<< JS
    window.__INITIAL_DATA__ = {};
JS;
//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_BEGIN);

?>


<script src="/statics/trends.js"></script>