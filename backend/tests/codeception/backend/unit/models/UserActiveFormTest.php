<?php

namespace tests\codeception\common\unit\models;

use Yii;
use tests\codeception\backend\unit\DbTestCase;
use Codeception\Specify;
use backend\models\form\UserActive;
use tests\codeception\backend\fixtures\UserFixture;

class UserActiveFormTest extends DbTestCase
{

    use Specify;

    public function setUp()
    {
        parent::setUp();

        Yii::configure(Yii::$app, [
            'components' => [
                'user' => [
                    'class' => 'yii\web\User',
                    'identityClass' => 'common\models\User',
                ],
            ],
        ]);
    }

	public function testUserActiveNoToken()
	{
		$model = new UserActive([
			'token' => 'not_existing_token'
		]);

		$this->specify('user token should not go', function () use ($model) {
			expect('model should not activate user', $model->activate())->false();
		});
	}

	public function testUserActiveCorrect()
	{
		$model = new UserActive([
			'token' => 'ExzkCOaYc1L8IOBs4wdTGGbgNiG3Wz1I_1402312317'
		]);

		$this->specify('user token must go', function () use ($model) {
			expect('model should activate user', $model->activate())->true();
			expect('error message should not be set', $model->errors)->hasntKey('token');
		});
	}

    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/codeception/backend/unit/fixtures/data/models/user.php'
            ],
        ];
    }

}
