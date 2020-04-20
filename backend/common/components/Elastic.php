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

    public function searchReferences(array $params): array 
    {
        $query_params = [
            'index' => 'comments',
            'body'  => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        "filter" => [
                            [
                                "bool" => [
                                    "must" => [
                                        [
                                            "query_string" => [
                                                "default_field" => "text",  
                                                "query" => "*маски*"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "aggs" => [
                    "references" => [
                        "date_histogram" => [
                            "field" => "@timestamp",
                            "calendar_interval" => "day"
                        ]
                    ]
                ]
            ]
        ];

        if (isset($this->range_start) || isset($this->range_end)) {
            $query_params = $this->appendRangeToQuery($query_params);
        }

        $resp = $this->client->search($query_params);
        
        $res = [];

        foreach($resp['aggregations']['references']['buckets'] as $item) {
            $res[] = [
                'time_key' => $item['key_as_string'],
                'timestamp_key' => $item['key'],
                'count' => $item['doc_count']
            ];
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
}