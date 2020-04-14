<?php

use yii\db\Migration;

/**
 * Handles the creation of table `city`.
 */
class m200413_203606_create_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('city', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'lngc' => $this->string(),
            'ltdc' => $this->string(),
            'lng1' => $this->string(),
            'ltd1' => $this->string(),
            'lng2' => $this->string(),
            'ltd2' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('city');
    }
}
