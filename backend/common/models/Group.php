<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "group".
 *
 * @property int $id
 * @property string $name
 * @property int $vk_group_id
 * @property int $city_id
 * @property int $category_id
 * @property int $created_at
 * @property int $updated_at
 */
class Group extends \yii\db\ActiveRecord
{

    public $category_ids = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vk_group_id', 'city_id', 'category_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['category_ids', 'safe']
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
            'vk_group_id' => 'Vk Group ID',
            'city_id' => 'City ID',
            'category_id' => 'Category ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function setCategoryIds()
    {
        $rows = (new \yii\db\Query())
            ->select(['category_id'])
            ->from('group_to_category')
            ->all();

        $ids = array_map(function ($item) {
            return $item['category_id'];
        }, $rows);

        $this->category_ids = $ids;
    }

    public function saveIds($ids)
    {
        Yii::$app->db->createCommand()->delete('group_to_category', 'group_id = ' . $this->id)->execute();

        if (count($ids) === 0) {
            return false;
        }

        $rows = [];

        foreach($ids as $id) {
            $rows[] = [$this->id, $id];
        }

        Yii::$app->db->createCommand()->batchInsert('group_to_category', ['group_id', 'category_id'], $rows)->execute();
    }
}
