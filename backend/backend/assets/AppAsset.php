<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Основные ресурсы, используемые в админке
 *
 * Class AppAsset
 * @package backend\assets
 */
class AppAsset extends AssetBundle
{
    /**
     * @var string базовый путь к файлам
     */
    public $basePath = '@webroot';
    /**
     * @var string базовый url
     */
    public $baseUrl = '@web';
    /**
     * @var array подключенные css ресурсы
     */
    public $css = [
    ];
    /**
     * @var array подключенные js ресурсы
     */
    public $js = [
    ];
    /**
     * @var array зависимости админки
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
