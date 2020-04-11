<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id'                    => $this->primaryKey() ,
            'username'              => $this->string()->notNull() . ' COMMENT "имя пользователя"',
            'auth_key'              => $this->string(32)->notNull() . ' COMMENT "ключ"',
            'password_hash'         => $this->string() . ' COMMENT "хеш пароль"',
            'password_reset_token'  => $this->string() . ' COMMENT "токен для сброса пароля"',
            'email'                 => $this->string()->notNull() . ' COMMENT "электронная почта пользователя"',
            'role'                  => $this->string(10) . ' COMMENT "роль пользователя"',
            'status'                => $this->smallInteger()->notNull()->defaultValue(10) . ' COMMENT "статус пользователя"',
            'created_at'            => $this->integer() . ' COMMENT "дата создания"',
            'updated_at'            => $this->integer() . ' COMMENT "дата обновления"',
        ], $tableOptions);

        $this->insert('{{%user}}',[
            'username'      => 'admin',
            'auth_key'      => 'regpb8Px8S7fD7jwN4Yilc_5G6iHIEYQ',
            'password_hash' => '$2y$13$Vorp1D6.leU3SNHoOhGNB.xTEWphsRrQEQ07dTws/LCQXyQfstOJe',
            'email'         => 'admin@admin.ru',
	        'role'          => 'admin',
            'status'        => '1',
            'created_at'    => time(),
            'updated_at'    => time(),
        ]); // admin/123456

    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
