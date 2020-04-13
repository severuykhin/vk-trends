<?php

namespace common\workers;

class GroupProcess 
{

    private $worker_host = 'http://localhost:8000';

    public function run(int $group_id) 
    {
       $url = $this->worker_host . '/api/process/' . $group_id;

       $resp = $this->send($url);

       return $resp;
    }

    private function send($url)
    {
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        //execute post
        $result = curl_exec($ch);
        return $result;
    }
}