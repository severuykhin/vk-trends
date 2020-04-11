const axios = require('axios');

class VkApi {

    constructor() {

        this.tokens = [
            '7e0e5675b30187b871972e352e61eeb7316041c47d2f6340b890d7b7193ffd1644dc85c5a68e0d4c1c2a3',
            'ef4b27ffb546c2c43d12f61fc97dabef2f06a335397a253aad6fa0688332011850847d636edb846c8dfc0',
            'f5424cee73d4ee82521d952d0df9e68c3fb0d0a1a8570c4a2ef77535fcdd2921b80915c4af213fe0ee8d0',
            'bfcfe8ab47ff9356f705658a3a0457ee606d254cabc1d5df40d304e6c5f0373617600945432ec31c7b737',
            'b2526821bc96107cace445dc1dfe9e6a30d9eeea0fafbccab150bd63df7bca02a591deecbf673df141c74',
            '7c1bb6a9b0d0b459b529cc6adbb1646d591409f9a02ac29d83a955a79c43ff887dafcb92d1f53d61bc7ba',
            '1d97c9c24c7d48affc79ab5e6eb8b87c1cf6808e9af1939eb5ab04b7778c82912b8f704e6b1eaaebe72cf',
            '2d9d54156fa55a9965a2d7cf228da4b604ee1d09767aa8afe6c93dfa482f4e98e24c6329b475c3fdf5999',
            '317fd30f619310f4a6d654ea419b2ca00ea775cf56c63f00b685de2a940069def0abde97020ac1967066c',
            'b4ccb301369733c20c36f300047f131ed0cdd9d30904657e51ef7cc8ba6aaa275b28af3221057f11caeeb'

        ];

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
        const response = await axios.get('https://api.vk.com/method/execute', {
            params: {
                'code': this.getWallPostsIdsExecuteCode(),
                'group': `-${vk_group_id}`,
                'period': 7,
                'access_token': this.getToken(),
                'v': '5.103'
            }
        });

        return response.data;
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

            while(result.items.length < total_count && count <= 24) {

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