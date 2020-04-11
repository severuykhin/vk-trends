<?php

namespace common\components\queues\jobs;

use yii\base\BaseObject;

class ReportsJob extends BaseObject implements \yii\queue\JobInterface
{
    public $id;
    public $group_id;
    public $group_config;
    
    public function execute($queue)
    {
        var_dump($this);
    }
}