<?php

use yii\db\Migration;

/**
 * Handles the creation of table `group_to_category`.
 */
class m200413_195824_create_group_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('group_to_category', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('group_to_category');
    }
}
