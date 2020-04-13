const axios = require('axios');
const constants = require('../includes/constants');
const amqp = require('amqplib/callback_api');
const VkApi = require('./VkApi');
const Mongo = require('./Mongo');

class GroupWorker {

    constructor(producer) {
        this.producer = producer;
    }

    async run(group_id) {

        const group_config = this.getGroupConfig(group_id);
        
        const response = await VkApi.getWallPostsIds(group_id)

        if (response.result === 'error') {
            return response.result;
        }
        
        const items = response.response.response.items;
        const posts = response.response.response.posts;

        const report_id = `${Date.now()}-${group_id}`;

        const distinct_ids = new Set(items);
        const ids_array = Array.from(distinct_ids);

        let report;

        try {
            const reportsCollection = Mongo.getReportsCollection();

            if (reportsCollection) {
                report = await reportsCollection.insertOne({
                    type: 'comments',
                    report_id: report_id,
                    total: ids_array.length,
                    errors: 0,
                    success: 0,
                    time_start: new Date(),
                    time_end: null,
                    status: 0
                });
            }

        } catch(e) {
            return {
                'result': 'error',
                'message': e.message
            };
        }

        ids_array.forEach(async (item, index) => {
            const msg = {
                type: 'comments',
                report_id: report_id,
                total: items.length,
                index: index + 1,
                post_id: item,
                group_config: group_config,
            };        
            this.producer.send(msg);        
        });

        // Фильтр постов на уникальность

        console.log(posts);

        posts.forEach(async (post, index) => {
            const msg_post = {
                type: 'posts',
                report_id: report_id,
                group_config: group_config,
                data: post
            };
            this.producer.send(msg_post);        
        });

        return report;

    }

    getGroupConfig(group_id) { // Нужно получать из базы
        return {
            vk_group_id: group_id,
            like_first_points: 2,
            like_rest_points: 1,
            comment_first_points: 3,
            comment_rest_points: 2,
            max_user_post_comments: 100,
            max_user_post_comment_row: 3,
            min_comment_length: 10,
            comment_user_likes: 10,
            comment_user_likes_point: 1,
            comment_user_likes_point_max: 3,
            vote_points: 1,
            post_points: 1,
            show_places: 10,
            repost_points: 10,
            widget_type: 'test', // Тип отображения
            widget_columns: 1, // 1 место и баллы, 2 только место, 3 только баллы
            widget_last_row: 1,

        };
    }

    
}

module.exports = GroupWorker;