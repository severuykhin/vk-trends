<?php

use tests\codeception\backend\AcceptanceTester;
use tests\codeception\backend\_pages\LoginPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure login page works');

$loginPage = LoginPage::openBy($I);

$I->amGoingTo('submit login form with no data');
$loginPage->login('', '');
if (method_exists($I, 'wait')) {
    $I->wait(3);
}
$I->expectTo('see validations errors');
$I->see('Необходимо заполнить «имя пользователя».', '.help-block');
$I->see('Необходимо заполнить «пароль».', '.help-block');

$I->amGoingTo('try to login with wrong credentials');
$I->expectTo('see validations errors');
$loginPage->login('admin', 'wrong');
if (method_exists($I, 'wait')) {
    $I->wait(3);
}
$I->expectTo('see validations errors');
$I->see('Incorrect username or password.', '.help-block');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('erau', 'password_0');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see that user is logged');
$I->click('Выход');
$I->dontSeeLink('Выход');
$I->see('Войти','button[type="submit"]');