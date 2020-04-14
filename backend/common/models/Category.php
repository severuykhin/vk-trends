<?php

namespace common\models;

use Yii;
use voskobovich\behaviors\ManyToManyBehavior;
use yii\helpers\ArrayHelper;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use creocoder\nestedsets\NestedSetsBehavior;
use vova07\fileapi\behaviors\UploadBehavior;
use yii\helpers\VarDumper;
/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name название
 * @property string $slug slug
 * @property string $description описание
 * @property int $lft слева
 * @property int $rgt справа
 * @property int $level уровень
 * @property int $created_at дата создания
 * @property int $updated_at дата редактирования
 */
class Category extends \yii\db\ActiveRecord
{

    public $parent_id = 1;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class
            ],
            [
                'class' => NestedSetsBehavior::class,
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'level',
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'immutable' => true,
                'ensureUnique' => true
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['lft', 'rgt', 'level', 'created_at', 'updated_at', 'parent_id'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 300],
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
            'description' => 'Description',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'level' => 'Level',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function CategoryArray()
    {
        $array = self::find()->select(['id', 'name']);
        if ($this->id) {
            $array = $array->orWhere(['<', 'lft', $this->lft])->orWhere(['>', 'rgt', $this->rgt]);
        }
        $array = $array->orderBy('level')->asArray()->all();
        return ArrayHelper::map($array, 'id', 'name');
    }

    public static function getIds()
    {
        $array = self::find()->select(['id', 'name']);
        $array = $array->orderBy('level')->asArray()->all();
        return ArrayHelper::map($array, 'id', 'name');
    }
}
