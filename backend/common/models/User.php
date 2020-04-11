<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

/**
 * Модель для таблицы "user"
 * Class Category
 * @package common\models
 */
class User extends ActiveRecord implements IdentityInterface
{

    public $password;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;
    const STATUS_DELETED = 3;

    const ROLE_DEFAULT = 'user';

    /**
     * @return string имя таблицы
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['role', 'default', 'value' => self::ROLE_DEFAULT],
            [['username', 'email', 'role', 'status'], 'required'],
            [['username'], 'unique'],
            [['email'], 'email'],
            ['password', 'string'],
            [['username', 'email'], 'string', 'max' => 255],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE, self::STATUS_BANNED, self::STATUS_DELETED]],
        ];
    }

    /**
     * Расшифровка атрибутов
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => 'имя пользователя',
            'email' => 'электронная почта',
            'status' => 'статус',
            'statusName' => 'статус',
            'created_at' => 'добавлен',
            'updated_at' => 'обновлен',
            'role' => 'роль',
            'password' => 'пароль'
        ];
    }

    /**
     * Поиск идентифицированного и активного пользователя
     * @param int|string $id
     * @return null|IdentityInterface|static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Поиск по токену пользователя
     * @param mixed $token
     * @param null $type
     * @return void|IdentityInterface
     * @throws \yii\base\NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Поиск по имени пользователя
     * @param $username имя пользователя
     * @return null|static
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Поиск по пароль-токену
     * @param $token
     * @return null|static
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Проверка токена на правильность
     * @param $token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * Получить id пользователя
     * @return int|mixed|string
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * получить ключ авторизации
     * @return mixed|string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * проверка ключа авторизации
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Проверка пароля
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Установка пароля
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Генерация ключа авторизации
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Генерация токена сброса пароля
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Удаление токена сброса пароля
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Получение текстового статуса пользователя
     * @return string
     */
    public function getStatusName()
    {
        $statusName = '';
        switch ($this->status) {
            case '0': $statusName = 'не активирован'; break;
            case '1': $statusName = 'активен'; break;
            case '2': $statusName = 'забанен'; break;
            case '3': $statusName = 'удален'; break;
            default: $statusName = 'неизвестный';
        }
        return $statusName;
    }

    /**
     * Удаление пользователя
     * @return false|int|void
     */
    public function delete()
    {
        if (!$this->isNewRecord) {
            $this->status = self::STATUS_DELETED;
            $this->save();
        }
    }

    /**
     * Активация пользователя
     */
    public function active()
    {
        if (!$this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
            $this->save();
        }
    }

    /**
     * Бан пользователя
     */
    public function ban()
    {
        if (!$this->isNewRecord) {
            $this->status = self::STATUS_BANNED;
            $this->save();
        }
    }

}
