<?php

namespace backend\config\rbac\rules;

use Yii;
use yii\rbac\Rule;

/**
 * Управление группами пользователей
 *
 * Class GroupRule
 * @package backend\config\rbac\rules
 */
class GroupRule extends Rule
{
    /**
     * @var string название группы
     */
    public $name = 'group';

    /**
     * Проверка прав доступа
     *
     * @param int|string $user пользователь
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        if (!Yii::$app->user->isGuest) {
            $role = Yii::$app->user->identity->role;
            if ($item->name === 'admin') {
                return $role === $item->name;
            } else if ($item->name === 'user') {
                return $role === $item->name || $role === 'admin';
            }
        }
        return false;
    }
}
