<?php

use yii\db\Migration;

/**
 * Handles the creation of table `group`.
 */
class m200411_113250_create_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('group', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'vk_group_id' => $this->integer()->notNull()->defaultValue(0),
            'city_id' => $this->integer(),
            'category_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->createTable('token', [
            'id' => $this->primaryKey(),
            'value' => $this->string(),
            'status' => $this->integer(),
            'errors_count' => $this->integer(),
            'requests_count' => $this->integer() 
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('group');
        $this->dropTable('token');
    }
}
