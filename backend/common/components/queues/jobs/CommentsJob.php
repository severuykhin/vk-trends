<?php

namespace common\components\queues\jobs;

use yii\base\BaseObject;

class CommentsJob extends BaseObject implements \yii\queue\JobInterface
{
    public $type;
    public $report_id;
    public $total;
    public $index;
    public $post_id;
    public $group_config;
    
    public function execute($queue)
    {
        var_dump($this);
    }
}