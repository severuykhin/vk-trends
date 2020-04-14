<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $lngc
 * @property string $ltdc
 * @property string $lng1
 * @property string $ltd1
 * @property string $lng2
 * @property string $ltd2
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'lngc', 'ltdc', 'lng1', 'ltd1', 'lng2', 'ltd2'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'lngc' => 'Lngc',
            'ltdc' => 'Ltdc',
            'lng1' => 'Lng1',
            'ltd1' => 'Ltd1',
            'lng2' => 'Lng2',
            'ltd2' => 'Ltd2',
        ];
    }

    public static function getIds()
    {
        $array = self::find()->select(['id', 'name']);
        $array = $array->orderBy('name')->asArray()->all();
        return ArrayHelper::map($array, 'id', 'name');
    }
}
