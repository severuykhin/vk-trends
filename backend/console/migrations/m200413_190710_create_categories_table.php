<?php

use yii\db\Migration;

/**
 * Handles the creation of table `categories`.
 */
class m200413_190710_create_categories_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('category', [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(300)->notNull()->comment('название'),
            'slug'        => $this->string(300)->comment('slug'),
            'description' => $this->text()->comment('описание'),
            'lft'         => $this->bigInteger()->comment('слева'),
            'rgt'         => $this->bigInteger()->comment('справа'),
            'level'       => $this->bigInteger()->comment('уровень'),
            'created_at'  => $this->integer()->defaultValue(time())->comment('дата создания'),
            'updated_at'  => $this->integer()->defaultValue(time())->comment('дата редактирования')
        ], $tableOptions);

        $this->insert('category', [
            'name'  => 'без категории',
            'lft'   => '1',
            'rgt'   => '2',
            'level' => '1'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('category');
    }
}
