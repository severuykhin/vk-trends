<?php

namespace backend\models\form;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Модель формы авторизации
 * @package backend\models\form
 */
class Login extends Model
{
    /**
     * @var string имя пользователя
     */
    public $username;

    /**
     * @var string пароль
     */
    public $password;

    /**
     * @var bool запоминать пользователя
     */
    public $rememberMe = true;

    /**
     * @var object пользовательский объект
     */
    private $_user = false;

    /**
     * правила валидации
     * @return array правила валидации
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Расшифровка атрибутов
     * @return array
     */
    public function attributeLabels()
	{
		return [
			'username' => Yii::t('common', 'Username'),
			'password' => Yii::t('common', 'Password'),
			'rememberMe' => Yii::t('common', 'rememberMe')
		];
	}

    /**
     * Проверка пароля
     * @param $attribute
     * @param $params
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Вход в админку
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Полученние пользователя
     * @return null|object|static
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

}
