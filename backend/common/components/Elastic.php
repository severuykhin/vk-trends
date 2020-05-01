<?php

namespace common\components;

use Elasticsearch\ClientBuilder;

class Elastic 
{

    const SOURCES = ['comments', 'posts', 'boards'];
    const INDEX_VERSION = 2;


    private $client;

    private $range_start;

    private $range_end;

    private $cities;

    private $categories;

    private $calendar_interval;

    private $split_cities;

    private $search_type;

    private $accuracy;

    private $source;

    const LOCAL_ELASTIC_HOSTS = [
        '92.53.104.20:9200'
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
        $this->cities = isset($params['cities']) ? explode(',', $params['cities']) : null;
        $this->categories = isset($params['categories']) ? explode(',', $params['categories']) : null;
        $this->calendar_interval = isset($params['ci']) && $this->isValidCi($params['ci']) ? $params['ci'] : 'day';
        $this->split_cities = isset($params['split_cities']) ? intval($params['split_cities']) : 0;
        $this->search_type = isset($params['st']) ? $params['st'] : 'word';
        $this->accuracy = isset($params['accuracy']) ? $this->getAccuracy($params['accuracy']) : 1; 
        $this->source = isset($params['source']) ? $this->getSources($params['source']) : ['comments'];

    }

    private function isValidCi(string $calendar_interval): bool
    {
        return in_array($calendar_interval, ['day', 'year', 'week', 'hour']);
    }

    private function getAccuracy($accuracy_input_value) 
    {
        $cases = [
            1 => 10,
            2 => 9,
            3 => 8,
            4 => 7,
            5 => 6,
            6 => 5,
            7 => 4,
            8 => 3,
            9 => 2,
            10 => 1,
        ];

        if (isset($cases[$accuracy_input_value])) {
            return $cases[$accuracy_input_value];
        }

        return 1;
        
    }

    private function getSources($sources_input_value)
    {
        $res = [];

        $sources_input_value = explode(',', $sources_input_value);

        foreach($sources_input_value as $source) {
            if (in_array($source, self::SOURCES)) {
                $res[] = $source;
            }
        }

        return $res;

    }

    public function searchReferences(array $params): array 
    {

        $res = [
            'total' => 0,
            'references_total' => [],
            'references_by_cities' => [],
            'sample' => []
        ];

        foreach($this->source as $index) {

            $query_params = [
                'index' => $index . '' . self::INDEX_VERSION,
                'body'  => [
                    'size' => 10,
                    'query' => [
                        'bool' => [
                            "filter" => [
                                
                            ]
                        ]
                    ],
                    "aggs" => [
                        "references_total" => [
                            "date_histogram" => [
                                "field" => "@timestamp",
                                "calendar_interval" => $this->calendar_interval
                            ]
                        ]
                    ]
                ]
            ];
    
            if ($this->search_type === 'word') {
                $query_params['body']['query']['bool']['filter'][] = [
                    "bool" => [
                        "must" => [
                            [
                                "query_string" => [
                                    "default_field" => "text",  
                                    "query" => "*" . $params['query'] . "*",
                                    "boost" => 1
                                ]
                            ]
                        ]
                    ]
                ];
            } else if ($this->search_type === 'phrase') {
                $query_params['body']['query']['bool']['filter'][] = [
                    "bool" => [
                        "must" => [
                            [
                                "match_phrase" => [
                                    "text" => [
                                        "query" => $params['query'],
                                        "slop" => $this->accuracy,
                                        "boost" => 1
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            }
    
            if (isset($this->range_start) || isset($this->range_end)) {
                $query_params = $this->appendRangeToQuery($query_params);
            }
    
            if (isset($this->cities)) {
                $query_params = $this->appendCitiesToQuery($query_params);
            }
    
            if (isset($this->categories)) {
                $query_params = $this->appendCategoriesToQuery($query_params);
            }
    
            if ($this->split_cities > 0) {
                $query_params = $this->appendSplitCitiesAggregation($query_params);
            }
    
            $resp = $this->client->search($query_params);

            // return $resp;

            $res['total'] = (int)$res['total'] + (int)$resp['hits']['total']['value'];
            $res['sample'] = array_merge($res['sample'], $resp['hits']['hits']);

            foreach($resp['aggregations']['references_total']['buckets'] as $item) {

                if (isset($res['references_total'][$item['key']])) {
                    $res['references_total'][$item['key']]['count'] = $res['references_total'][$item['key']]['count'] + $item['doc_count'];
                } else {
                    $res['references_total'][$item['key']] =  [
                        'time_key' => $item['key_as_string'],
                        'timestamp_key' => $item['key'],
                        'count' => $item['doc_count']
                    ];
                }
            }

            if ($resp['aggregations']['references_by_cities']) {

                foreach($resp['aggregations']['references_by_cities']['buckets'] as $bucket) {
                    
                    if (isset($res['references_by_cities'][$bucket['key']])) {
                        
                        $res['references_by_cities'][$bucket['key']]['count'] = $res['references_by_cities'][$bucket['key']]['count'] + $bucket['doc_count'];
                        
                        foreach($bucket['references']['buckets'] as $item) {
                            if (isset($res['references_by_cities'][$bucket['key']]['items'][$item['key']])) {
                                $res['references_by_cities'][$bucket['key']]['items'][$item['key']]['count'] = $res['references_by_cities'][$bucket['key']]['items'][$item['key']]['count'] + $item['doc_count'];
                            } else {
                                $res['references_by_cities'][$bucket['key']]['items'][$item['key']] = [
                                    'time_key' => $item['key_as_string'],
                                    'timestamp_key' => $item['key'],
                                    'count' => $item['doc_count']
                                ];
                            }
                        }
                    
                    } else {
                        $res['references_by_cities'][$bucket['key']] = [
                            'key' => $bucket['key'],
                            'count' => $bucket['doc_count'],
                            'items' => []
                        ];

                        foreach($bucket['references']['buckets'] as $item) {
                            $res['references_by_cities'][$bucket['key']]['items'][$item['key']] = [
                                'time_key' => $item['key_as_string'],
                                'timestamp_key' => $item['key'],
                                'count' => $item['doc_count']
                            ];
                        }
                    }
                
                }
            }
        }

        return $res;
    }

    public function getGroupPostsVerbalPortrait(array $params): array 
    {

        $query_params = [
            'index' => 'posts',
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

    public function getGroupSummary(array $params): array
    {

        $res = [];

        $query_params_posts = [
            'index' => 'posts',
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
                    "posts_count" => [
                        "cardinality" => [
                          "field" => "vk_id"
                        ]
                    ],
                    "views_per_post" => [
                        "avg" => [
                            "field" => "views"
                        ]    
                    ],
                    "likes_per_post" => [
                        "avg" => [
                            "field" => "likes"
                        ]    
                    ],
                    "reposts_per_post" => [
                        "avg" => [
                            "field" => "reposts"
                        ]    
                    ],
                ]
            ]
        ];

        if (isset($this->range_start) || isset($this->range_end)) {
            $query_params_posts = $this->appendRangeToQuery($query_params_posts);

        }

        $posts_resp = $this->client->search($query_params_posts);

        $query_params_comments = [
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
                    "comments_count" => [
                        "cardinality" => [
                          "field" => "vk_id"
                        ]
                    ]
                ]
            ]
        ];

        if (isset($this->range_start) || isset($this->range_end)) {
            $query_params_comments = $this->appendRangeToQuery($query_params_comments);

        }

        $comments_resp = $this->client->search($query_params_comments);
    
        if (isset($comments_resp['aggregations'])) {
            $res['comments_count'] = $comments_resp['aggregations']['comments_count']['value'];
        }

        if (isset($posts_resp['aggregations'])) {
            $res['posts_count'] = floor($posts_resp['aggregations']['posts_count']['value']);
            $res['likes_per_post'] = floor($posts_resp['aggregations']['likes_per_post']['value']);
            $res['reposts_per_post'] = floor($posts_resp['aggregations']['reposts_per_post']['value']);
            $res['views_per_post'] = floor($posts_resp['aggregations']['views_per_post']['value']);
        }

        if ($res['posts_count'] && $res['comments_count']) {
            $res['comments_per_post'] = floor($res['comments_count'] / $res['posts_count']);
        } else {
            $res['comments_per_post'] = 0;
        }

        return $res;
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
            $query_params = $this->appendRangeToQuery($query_params);
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

    public function getTopСommentatorSummary(array $params): array
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
                    "top_commentators" => [
                        "terms" => [
                            "field" => "from_id",
                            "size" => 10
                        ],
                        "aggs" => [
                            "keywords" => [
                                "terms" => [
                                    "field" => "keys.keyword",
                                    "order" => [
                                        "_count" => "desc"
                                ],
                                "size" => 10
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if (isset($this->range_start) || isset($this->range_end)) {
            $query_params = $this->appendRangeToQuery($query_params);
        }

        $response = $this->client->search($query_params);
        $res = [];

        foreach($response['aggregations']['top_commentators']['buckets'] as $item) {

            $user = [
                'key' => $item['key'],
                'vk_user_id' => $item['key'],
                'value' => $item['doc_count'],
                'keywords' => []
            ];

            if (isset($item['keywords']) && isset($item['keywords']['buckets'])) {
                foreach($item['keywords']['buckets'] as $keyword) {
                    $user['keywords'][] = [
                        'value' => $keyword['key'],
                        'count' => $keyword['doc_count']
                    ];
                }
            }

            $res[] = $user;
        }

        return $res;
    }

    private function appendRangeToQuery(array $query_params): array
    {
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

        return $query_params;
    }

    private function appendCitiesToQuery(array $query_params): array 
    {
        $cities_filter = [
            "bool" => [
                
            ]
        ];

        if (count($this->cities) > 1) {

            $cities_filter['bool']["should"] = [];

            foreach($this->cities as $city_id) {
                $cities_filter['bool']["should"][] = [
                    "match" => [
                        "city" => $city_id
                    ]
                ];
            }

        } else if (count($this->cities) === 1) {
            $cities_filter['bool']["must"] = [
                [
                    "match" => [
                        "city" => $this->cities[0]
                    ]
                ]
            ];
        }

        $query_params['body']['query']['bool']['filter'][] = $cities_filter;

        return $query_params;
    }
    
    private function appendCategoriesToQuery(array $query_params): array 
    {
        $cat_filter = [
            "bool" => [
                
            ]
        ];

        if (count($this->categories) > 1) {

            $cat_filter['bool']["should"] = [];

            foreach($this->categories as $c_id) {
                $cat_filter['bool']["should"][] = [
                    "match" => [
                        "categories" => $c_id
                    ]
                ];
            }

        } else if (count($this->categories) === 1) {
            $cat_filter['bool']["should"] = [
                [
                    "match" => [
                        "categories" => $this->categories[0]
                    ]
                ]
            ];
        }

        $query_params['body']['query']['bool']['filter'][] = $cat_filter;

        return $query_params;
    }

    private function appendSplitCitiesAggregation(array $query_params): array
    {
        $query_params['body']['aggs']["references_by_cities"] = [
            "terms" => ['field' => "city"],
            "aggs" => [
                "references" => [
                    "date_histogram" => [
                        "field" => "@timestamp",
                        "calendar_interval" => $this->calendar_interval
                    ]    
                ]
            ]
        ];

        return $query_params;
    }
}