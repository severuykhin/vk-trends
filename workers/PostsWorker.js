const VkApi = require('./VkApi');
const Mongo = require('./Mongo');
const Elastic = require('../services/Elastic');
const format = require('date-fns/format');

class PostsWorker {

    constructor(config) {
        this.config = config;
        this.collection = Mongo.getPostsCollection();
    }

    async run() {
        
        const post = {...this.config.data};
        post.report_id = this.config.report_id;

        const full_id = `${Math.abs(post.owner_id).toString()}_${post.id}`;

        post.full_id = full_id;

        const isExists = await this.collection.find({id: post.id}, {_id: 1}).limit(1).count();

        if (isExists <= 0) {
            const res = await Elastic.indexPost(post);
            if (res.statusCode === 201) {
                this.collection.insertOne(post);
            } else {
                console.log(res.meta.body.error);
            }
        }

        return true;
    }
}

module.exports = PostsWorker;