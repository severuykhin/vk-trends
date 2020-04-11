<?php

namespace console\controllers;

use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use common\components\queues\jobs\ReportsJob;


class TestController extends Controller
{

    public function actionIndex($group_id)
    {

        $group_data = Yii::$app->db->createCommand(
            'SELECT * FROM `vk_groups` WHERE `vk_group_id` = :id',
            [
                ':id' => $group_id
            ]
        )->queryOne();

        $port = null;

        if (!$group_data) {
            $port = $this->getFreePort();
            $res = Yii::$app->db->createCommand()->insert('vk_groups', [
                'vk_group_id' => $group_id,
                'daemon_port' => $port 
            ])->execute();
        } else {
            $port = (int) $group_data['daemon_port'];
        }

        $portIsOpen = $this->checkPortIsOpen($port);

        if ($portIsOpen) {
            $this->runDaemon($group_id, $port);
        } else {
            $this->sendTask($group_id, $port);
        }
    }

    private function runDaemon($vk_group_id, $port)
    {
        exec("cd /home/igor/git/vkgroups && pm2 start daemon.js " . $vk_group_id . " " . $port , $out, $err);
    }

    private function sendTask($vk_group_id, $port)
    {

    }

    private function getFreePort()
    {
        $sock = socket_create_listen(0); 
        socket_getsockname($sock, $addr, $port); 
        socket_close($sock);

        return $port;
    }

    private function checkPortIsOpen($port)
    {
        $connection = @fsockopen('localhost', $port);
        if (is_resource($connection)) {
            fclose($connection);
            return false;
        } else {
            return true;
        }
    }
}