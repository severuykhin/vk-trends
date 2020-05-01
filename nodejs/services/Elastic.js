const { Client } = require('@elastic/elasticsearch')
const stop_words = require('../includes/stop_words');
const moment = require('moment');

const time_format = 'yyyy-MM-dd HH:mm:ss.SSSSSS';
const time_format_moment = 'YYYY-MM-DD HH:mm:ss.SSSSSS';

const ElasticBoardBody = require('../mappings/ElasticBoardBody');

class Elastic {

    constructor() {
        const client = new Client({ node: 'http://92.53.104.20:9200' });
        this.client = client;
    }

    async createIndexes() {

        if (!this.client) {
            throw new Error('Elastic: no alive nodes find in your cluster');
        }

        try {
            let com_index_is_exists = await this.client.indices.exists({ index: 'comments2' });
            if (com_index_is_exists && com_index_is_exists.statusCode === 404) {
                await this.create_comments_index();
            }

            let posts_index_is_exists = await this.client.indices.exists({ index: 'posts2' });
            if (posts_index_is_exists && posts_index_is_exists.statusCode === 404) {
                await this.create_posts_index();
            }

            let boards_index_is_exists = await this.client.indices.exists({ index: 'boards2' });
            if (boards_index_is_exists && boards_index_is_exists.statusCode === 404) {
                await this.create_boards_index();
            }

        } catch (e) {
            console.log(e.meta.body.error);
        }


    }

    async create_comments_index() {
        return await this.client.indices.create({
            index: 'comments2',
            body: {
                'settings': {
                    'number_of_shards': 1,
                    'number_of_replicas': 1,
                    "analysis": {
                        "filter": {
                          "ru_stop": {
                            "type": "stop",
                            "stopwords": "_russian_"
                          },
                          "ru_stemmer": {
                            "type": "stemmer",
                            "language": "russian"
                          }
                        },
                        "analyzer": {
                          "default": {
                            "char_filter": [
                              "html_strip"
                            ],
                            "tokenizer": "standard",
                            "filter": [
                              "lowercase",
                              "ru_stop",
                              "ru_stemmer"
                            ]
                          }
                        }
                    }
                },
                'mappings': {
                    "properties": {
                        "mongo_id": {type: "text"},
                        "vk_id": { type: "long" },
                        "from_id": { type: "long" },
                        "report_id": { type: "text" },
                        "post_id": { type: "long" },
                        "owner_id": { type: "long" },
                        "full_id": { type: "text" },
                        "@timestamp": {
                            "type": "date",
                            "format": time_format
                        },
                        "index_time": {
                            "type": "date",
                            "format": time_format
                        },
                        "keys": {
                            "type": "text",
                            "norms": true,
                            "fields": {
                                "keyword": {
                                    "type": "keyword",
                                    "ignore_above": 1024
                                }
                            }
                        },
                        "categories": {
                            "type": "text",
                            "norms": true,
                            "fields": {
                                "keyword": {
                                    "type": "keyword",
                                    "ignore_above": 1024
                                }
                            }
                        },
                        "city": {"type": "text"},
                        "text": { "type": 'text' },
                        "likes": { "type": "integer" },
                        "length": { "type": "integer" },
                        "lng": {"type": "float"},
                        "ltd": {"type": "float"}
                    }
                }
            }
        });
    }

    async create_posts_index() {
        return await this.client.indices.create({
            index: 'posts2',
            body: {
                'settings': {
                    'number_of_shards': 1,
                    'number_of_replicas': 1,
                    "analysis": {
                        "filter": {
                          "ru_stop": {
                            "type": "stop",
                            "stopwords": "_russian_"
                          },
                          "ru_stemmer": {
                            "type": "stemmer",
                            "language": "russian"
                          }
                        },
                        "analyzer": {
                          "default": {
                            "char_filter": [
                              "html_strip"
                            ],
                            "tokenizer": "standard",
                            "filter": [
                              "lowercase",
                              "ru_stop",
                              "ru_stemmer"
                            ]
                          }
                        }
                      }
                },
                'mappings': {
                    "properties": {
                        "mongo_id": {type: "text"},
                        "vk_id": { type: "long" },
                        "from_id": { type: "long" },
                        "post_id": { type: "long" },
                        "report_id": { type: "text" },
                        "owner_id": { type: "long" },
                        "full_id": { type: "text" },
                        "@timestamp": {
                            "type": "date",
                            "format": time_format
                        },
                        "index_time": {
                            "type": "date",
                            "format": time_format
                        },
                        "keys": {
                            "type": "text",
                            "norms": true,
                            "fields": {
                                "keyword": {
                                    "type": "keyword",
                                    "ignore_above": 1024
                                }
                            }
                        },
                        "text": { "type": 'text' },
                        "likes": { "type": "integer" },
                        "views": { "type": "integer" },
                        "reposts": { "type": "integer" },
                        "length": { "type": "integer" },
                        "categories": {
                            "type": "text",
                            "norms": true,
                            "fields": {
                                "keyword": {
                                    "type": "keyword",
                                    "ignore_above": 1024
                                }
                            }
                        },
                        "city": {"type": "text"},
                        "lng": {"type": "float"},
                        "ltd": {"type": "float"}
                    }
                }
            }
        });
    }

    async create_boards_index() {
        return await this.client.indices.create({
            index: 'boards2',
            body: {
                'settings': {
                    'number_of_shards': 1,
                    'number_of_replicas': 1,
                    "analysis": {
                        "filter": {
                          "ru_stop": {
                            "type": "stop",
                            "stopwords": "_russian_"
                          },
                          "ru_stemmer": {
                            "type": "stemmer",
                            "language": "russian"
                          }
                        },
                        "analyzer": {
                          "default": {
                            "char_filter": [
                              "html_strip"
                            ],
                            "tokenizer": "standard",
                            "filter": [
                              "lowercase",
                              "ru_stop",
                              "ru_stemmer"
                            ]
                          }
                        }
                      }
                },
                'mappings': {
                    "properties": {
                        "mongo_id": {type: "text"},
                        "vk_id": { type: "long" },
                        "from_id": { type: "long" },
                        "report_id": { type: "text" },
                        "owner_id": { type: "long" },
                        "full_id": { type: "text" },
                        "@timestamp": {
                            "type": "date",
                            "format": time_format
                        },
                        "index_time": {
                            "type": "date",
                            "format": time_format
                        },
                        "keys": {
                            "type": "text",
                            "norms": true,
                            "fields": {
                                "keyword": {
                                    "type": "keyword",
                                    "ignore_above": 1024
                                }
                            }
                        },
                        "text": { "type": 'text' },
                        "length": { "type": "integer" },
                        "categories": {
                            "type": "text",
                            "norms": true,
                            "fields": {
                                "keyword": {
                                    "type": "keyword",
                                    "ignore_above": 1024
                                }
                            }
                        },
                        "city": {"type": "text"},
                        "lng": {"type": "float"},
                        "ltd": {"type": "float"}
                    }
                }
            }
        });
    }

    async indexComment(comment) {
        const res = await this.client.index({
            index: 'comments2',
            body: {
                "mongo_id": comment.mongo_id,
                "vk_id": comment.id,
                "post_id": comment.post_id,
                "from_id": comment.from_id,
                "report_id": comment.report_id ? comment.report_id : '',
                "owner_id": comment.owner_id,
                "full_id": `${Math.abs(comment.owner_id).toString()}_${comment.id}`,
                "@timestamp": this.getTimestamp(comment),
                "index_time": moment.utc(new Date()).format(time_format_moment),
                "keys": this.getSplittedText(comment.text),
                "text": comment.text,
                "likes": comment.likes ? comment.likes.count : 0,
                "length": comment.text ? comment.text.length : 0,
                "categories": comment.categories && comment.categories.length > 0 ? comment.categories : [],
                "city": comment.city ? comment.city : 0,
                "lng": comment.lng,
                "ltd": comment.ltd
            }
        });

        return res;
    }

    async indexPost(post) {
        const res = await this.client.index({
            index: 'posts2',
            body: {
                "mongo_id": post.mongo_id,
                "report_id": post.report_id ? post.report_id : '',
                "vk_id": post.id,
                "from_id": post.from_id,
                "owner_id": post.owner_id,
                "full_id": `${Math.abs(post.owner_id).toString()}_${post.id}`,
                "@timestamp": this.getTimestamp(post),
                "index_time": moment.utc(new Date()).format(time_format_moment),
                "keys": this.getSplittedText(post.text),
                "text": post.text,
                "likes": post.likes ? post.likes.count : 0,
                "length": post.text ? post.text.length : 0,
                "views": post.views && post.views.count ? post.views.count : 0,
                "reposts": post.reposts && post.reposts.count ? post.reposts.count : 0,
                "categories": post.categories && post.categories.length > 0 ? post.categories : [],
                "city": post.city ? post.city : 0,
                "lng": post.lng,
                "ltd": post.ltd
            }
        });

        return res;
    }

    async indexBoardComment(comment) {
        const res = await this.client.index({
            index: 'boards2',
            body: ElasticBoardBody(comment)
        });

        return res;
    }

    getTimestamp(item) {
        let timestamp = '';

        try {
            timestamp = moment.utc(new Date(item.date * 1000)).format(time_format_moment);
        } catch (e) {
            timestamp = moment.utc(new Date()).format(time_format_moment);
        }

        return timestamp;
    }

    getSplittedText(text) {

        if (!text) {
            return [];
        }

        const words = this.replaceQuestionMarks(text).replace(/".,\/#!$%\^&\*;:{}=\_`~()]/g, " ").split(' ');

        const clear = [];

        words.forEach(w => {
            let clear_word = w
                .trim()
                .replace(/(\[\S{1,}\])/gi, '')
                .replace(/(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff]|[\u0023-\u0039]\ufe0f?\u20e3|\ud83d|\u3299|\u3297|\u303d|\u3030|\u24c2|\ud83c[\udd70-\udd71]|\ud83c[\udd7e-\udd7f]|\ud83c\udd8e|\ud83c[\udd91-\udd9a]|\ud83c[\udde6-\uddff]|[\ud83c[\ude01-\ude02]|\ud83c\ude1a|\ud83c\ude2f|[\ud83c[\ude32-\ude3a]|[\ud83c[\ude50-\ude51]|\u203c|\u2049|[\u25aa-\u25ab]|\u25b6|\u25c0|[\u25fb-\u25fe]|\u00a9|\u00ae|\u2122|\u2139|\ud83c\udc04|[\u2600-\u26FF]|\u2b05|\u2b06|\u2b07|\u2b1b|\u2b1c|\u2b50|\u2b55|\u231a|\u231b|\u2328|\u23cf|[\u23e9-\u23f3]|[\u23f8-\u23fa]|\ud83c\udccf|\u2934|\u2935|[\u2190-\u21ff])/g, '')
                .replace(/[.,\/#!$%\^&\*;:{}=\_`~()\+-]/g, "")
                .trim();

            if (
                clear_word.length <= 2 ||
                stop_words.indexOf(clear_word.toLowerCase()) >= 0 ||
                this.isNumeric(clear_word)
            ) return false;

            return clear.push(clear_word.toLowerCase());
        });

        return clear;
    }

    replaceQuestionMarks(text) {
        while (text.indexOf("?") >= 0) { text = text.replace("?", ''); }
        while (text.indexOf('"') >= 0) { text = text.replace('"', ''); }
        return text;
    }

    hasNumber(w) {
        return /\d/.test(w);
    }

    isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

}

module.exports = new Elastic();