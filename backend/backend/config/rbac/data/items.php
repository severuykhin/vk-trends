<?php

use yii\rbac\Item;

return [
    'user' => [
        'type' => Item::TYPE_ROLE,
        'description' => 'Пользователи',
        'ruleName' => 'group',
    ],
    'admin' => [
        'type' => Item::TYPE_ROLE,
        'description' => 'Администратор',
        'ruleName' => 'group',
        'children' => [
            'user',
        ],
    ],
];
?>