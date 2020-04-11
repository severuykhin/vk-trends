
<?php

class VKGroups {

    static $token = 'ef4b27ffb546c2c43d12f61fc97dabef2f06a335397a253aad6fa0688332011850847d636edb846c8dfc0';

    public $settings = [];
    public $dateStart;
    public $dateEnd;

    public function __construct(array $settings)
    {
        $this->settings = $settings;

        $this->setDateRange();

    }

    private function setDateRange()
    {
        $dateStart = 1583712000;
        $dateEnd = 1584316800;
    }

    public function processComments()
    {
        $response = $this->request([
            'url' => 'https://api.vk.com/method/execute',
            'params' => [
                'code' => $this->getCode(),
                'group' => '-60609780',
                'period' => 7
            ]
        ]);

        $data = json_decode($response, true);
        // $posts = $data['response']['items'];

        // $lastItem = $posts[count($posts) - 1];

        var_dump($data['response']['response']['items']);
    }   

    public function run()
    {

    }

    private function getCode()
    {
        return 'var serverTime = API.utils.getServerTime();
        var period = 0;
        var _offset = parseInt(Args.offset) * 25000;


        
        if( parseInt(Args.period) == 0) {
          period = 1  * 86400;
        } else {
          period = parseInt(Args.period) * 86400;
        };
        var id = parseInt(Args.group);
        
        if(id == 0){
          return { "access": "error",  "response": [], "msg": "not found id group", "test": Args };
        }
        
        var members = API.wall.get({owner_id: id, v: "5.103",  count: "100", offset: _offset, extended: 1}); 
        var count = members.count;
        
        var response = {"count": count };
        response.id = members.items@.id;
        response.date = members.items@.date;
        
        if(response.date.length < 100 || response.date[99] < (serverTime - period)) {
          var i = 0;
          var respon = { id: []};
          while(response.date.length > i){
            if( response.date[i] > (serverTime - period) ){
              respon.id.push(response.id[i]);
            }
            i = i + 1;
          }
          return { "access": "ok", "response": { "count": respon.id.length, "items": respon.id }};
        }
        
        var offset = 100 + _offset;
        var temp = {};
        while( offset < (2400 + _offset) ){
          var resp = API.wall.get({owner_id: id, v: "5.103", count: "100", offset: offset});
          temp.id = resp.items@.id;
          temp.date = resp.items@.date;
          
          //return (serverTime - response.date[99]) < period;
          if(temp.date.length < 100 || temp.date[99] < (serverTime - period)) {
            offset = 2500 + _offset;
          } 
          //else {
            response.id = response.id + members.items@.id;
            response.date = response.date + members.items@.date;
          //}
          offset = offset + 100;
        }
        
        var i = 0;
        while(temp.date.length > i){
          if( temp.date[i] > (serverTime - period) ){
            response.id.push(temp.id[i]);
          }
          i = i + 1;
        }
        
        
        return { "access": "ok", "response":{ "count": response.id.length, "items": response.id }};';
    }

    private function request(array $config)
    {

        $params = $config['params'];
        $params['access_token'] = self::$token;
        $params['v'] = '5.103';

        $string = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}

$settings = [
    'group_id' => 60609780,
    'period' => 'daily',
    'date_start' => '',
    'date_end' => ''
];

$parser = new VKGroups($settings);

$parser->processComments();