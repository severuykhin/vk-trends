<?php

namespace common\components;

use Elasticsearch\ClientBuilder;

class Elastic 
{

    private $client;

    private $range_start;
    private $range_end;

    const LOCAL_ELASTIC_HOSTS = [
        'localhost:9200'
    ];

    public function __construct()
    {
        $this->client = ClientBuilder::create()
                ->setHosts(self::LOCAL_ELASTIC_HOSTS)
                ->build();
    }

    public function load(array $params)
    {
        $this->range_start = isset($params['range_start']) ? $params['range_start'] : null;
        $this->range_end = isset($params['range_end']) ? $params['range_end'] : null;
    }

    public function getGroupPostsVerbalPortrait(array $params): array 
    {

        $params = [
            'index' => 'posts',
            'body'  => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];

        return [

        ];
    }

    public function getGroupCommentsVerbalPortrait(array $params): array
    {

        $query_params = [
            'index' => 'comments',
            'body'  => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        "filter" => [
                            [
                              "match_phrase" => [
                                "owner_id" => -$params['vk_group_id']
                              ]
                            ]
                        ]
                    ]
                ],
                "aggs" => [
                    "keywords" => [
                        "terms" => [
                            "field" => "keys.keyword",
                            "order" => [
                                "_count" => "desc"
                        ],
                        "size" => 60
                        ]
                    ]
                ]
            ]
        ];

        if (isset($this->range_start) || isset($this->range_end)) {

            $datetime_range_filter = [
                "range" => [
                    "@timestamp" => [
                        "format" => "strict_date_optional_time"
                    ]
                ]
            ];

            if (isset($this->range_start)) {
                $datetime_range_filter['range']['@timestamp']['gte'] = $this->range_start;
            }

            if (isset($this->range_end)) {
                $datetime_range_filter['range']['@timestamp']['lte'] = $this->range_end;
            }

            $query_params['body']['query']['bool']['filter'][] = $datetime_range_filter;

        }
        
        $response = $this->client->search($query_params);
        $res = [];

        foreach($response['aggregations']['keywords']['buckets'] as $item) {
            $res[] = [
                'key' => $item['key'],
                'value' => $item['doc_count']
            ];
        }

        return $res;
    }
}