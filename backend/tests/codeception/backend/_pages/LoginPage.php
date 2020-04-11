<?php

namespace tests\codeception\backend\_pages;

use yii\codeception\BasePage;

class LoginPage extends BasePage
{
    public $route = 'site/login';

    public function login($username, $password)
    {
        $this->actor->fillField('input[name="Login[username]"]', $username);
        $this->actor->fillField('input[name="Login[password]"]', $password);
        $this->actor->click('button[type="submit"]');
    }
}
