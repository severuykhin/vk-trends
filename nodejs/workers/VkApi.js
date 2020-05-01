const axios = require('axios');
const keys = require('../config/api_keys');
const common_config = require('../config/common');

class VkApi {

    constructor() {
        this.tokens = [...keys];
        this.activeToken = 0;
    }

    getToken() {
        let res = this.tokens[this.activeToken];

        this.activeToken++;

        if (this.activeToken > this.tokens.length - 1) {
            this.activeToken = 0;
        }

        return res;
    }

    async getPostComments(post_id, vk_group_id) {
        const token = this.getToken();
        const response = await axios.get('https://api.vk.com/method/execute', {
            params: {
                'code': this.getPostCommentsExecuteCode(),
                'owner_id': `-${vk_group_id}`,
                'post_id': post_id,
                'need_likes': 1,
                'access_token': token,
                'v': '5.103',
                'count': 100,
                'thread_items_count': 10
            }
        });

        return response.data;

    }

    async getWallPostsIds(vk_group_id) {

        try {
            const response = await axios.get('https://api.vk.com/method/execute', {
                params: {
                    'code': this.getWallPostsIdsExecuteCode(),
                    'group': `-${vk_group_id}`,
                    'period': common_config.days_gap,
                    'access_token': this.getToken(),
                    'v': '5.103'
                }
            });

            return response.data;
        } catch(e) {
            return {
                result: 'error',
                message: e.message
            };
        }
    }

    async getBoardsIds(vk_group_id) {
        try {
            const response = await axios.get('https://api.vk.com/method/board.getTopics', {
                params: {
                    'group_id': `${vk_group_id}`,
                    'order': 1,
                    'access_token': this.getToken(),
                    'v': '5.103'
                }
            });

            if (response.data.response && response.data.response.items) {
                return response.data.response.items.map(i => i.id);
            } else {
                return {
                    result: 'error',
                    message: `Error while getting boards for group ${vk_group_id}`    
                };
            } 
        } catch(e) {
            return {
                result: 'error',
                message: e.message
            };
        }
    }

    async getBoardComments(board_id, vk_group_id) {
        try {
            const response = await axios.get('https://api.vk.com/method/execute', {
                params: {
                    'code': this.getBoardCommentsExecuteCode(),
                    'group_id': `${vk_group_id}`,
                    'topic_id': `${board_id}`,
                    'access_token': this.getToken(),
                    'v': '5.103'
                }
            });
            return response.data;
        } catch(e) {
            return {
                result: 'error',
                message: e.message
            };
        }
    }

    getBoardCommentsExecuteCode() {
        return `
            var _offset = 0;
            var resp = API.board.getComments({
                group_id: Args.group_id,
                topic_id: Args.topic_id, 
                v: "5.103",  
                count: "100", 
                offset: _offset, 
                extended: 1,
                sort: 'asc'
            });

            if (resp.errors) {
                return {
                    result: "error",
                    errors: resp.errors
                };
            };

            var total_count = resp.count;
            var resp_count = resp.items.length;

            var result = {
                items: resp.items,
                total_count: resp.count
            };

            if (total_count <= resp_count) {
                return result;
            };

            var count = 1;

            while(result.items.length < total_count && count <= 20) {

                _offset = _offset + 100;

                var resp = API.board.getComments({
                    group_id: Args.group_id,
                    topic_id: Args.topic_id, 
                    v: "5.103",  
                    count: "100", 
                    offset: _offset, 
                    extended: 1,
                    sort: 'asc'
                });

                if (resp.items) {
                    var i = 0;
                    while (i < resp.items.length) {
                        result.items.push(resp.items[i]);
                        i = i + 1;
                    };
                };

                count = count + 1;
            }

            result.api_request_count = count;

            return result;
        `;
    }

    getPostCommentsExecuteCode() {
        return `
            var _offset = 0;
            var resp = API.wall.getComments({
                owner_id: Args.owner_id,
                post_id: Args.post_id, 
                v: "5.103",  
                count: "100", 
                offset: _offset, 
                extended: 1,
                need_likes: 1,
                thread_items_count: 10
            });

            if (resp.errors) {
                return {
                    result: "error",
                    errors: resp.errors
                };
            };

            var total_count = resp.current_level_count;
            var resp_count = resp.items.length;

            var result = {
                items: resp.items,
                first_level_count: resp.current_level_count,
                total_count: resp.count
            };

            if (total_count <= resp_count) {
                return result;
            };

            var count = 1;

            while(result.items.length < total_count && count <= 20) {

                _offset = _offset + 100;

                var resp = API.wall.getComments({
                    owner_id: Args.owner_id,
                    post_id: Args.post_id, 
                    v: "5.103",  
                    count: "100", 
                    offset: _offset, 
                    extended: 1,
                    need_likes: 1,
                    thread_items_count: 10
                }); 

                if (resp.items) {
                    var i = 0;
                    while (i < resp.items.length) {
                        result.items.push(resp.items[i]);
                        i = i + 1;
                    };
                };

                count = count + 1;
            }

            result.api_request_count = count;

            return result;

        `;
    }

    getWallPostsIdsExecuteCode() {
        return `var serverTime = API.utils.getServerTime();
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
            response.posts = members.items;
            
            if(response.date.length < 100 || response.date[99] < (serverTime - period)) {
            var i = 0;
            var respon = { id: [], posts: []};
            while(response.date.length > i){
                if( response.date[i] > (serverTime - period) ){
                    respon.id.push(response.id[i]);
                    respon.posts.push(response.posts[i]);
                }
                i = i + 1;
            }
                return { "access": "ok", "response": { "count": respon.id.length, "items": respon.id, "posts": respon.posts }};
            }
            
            var offset = 100 + _offset;
            var temp = {};
            while( offset < (2400 + _offset) ){
                var resp = API.wall.get({owner_id: id, v: "5.103", count: "100", offset: offset});
                temp.id = resp.items@.id;
                temp.posts = resp.items;
                temp.date = resp.items@.date;
            
            //return (serverTime - response.date[99]) < period;
            if(temp.date.length < 100 || temp.date[99] < (serverTime - period)) {
                offset = 2500 + _offset;
            } 
            //else {
                response.id = response.id + members.items@.id;
                response.date = response.date + members.items@.date;
                response.posts = response.posts + members.items;
            //}
            offset = offset + 100;
            }
            
            var i = 0;
            while(temp.date.length > i){
                if( temp.date[i] > (serverTime - period) ){
                    response.id.push(temp.id[i]);
                    response.posts.push(temp.posts[i]);
                }
                i = i + 1;
            }
            
            
            return { "access": "ok", "response":{ "count": response.id.length, "items": response.id, "posts": response.posts }};`;
    }
}

const instance = new VkApi();

module.exports = instance;