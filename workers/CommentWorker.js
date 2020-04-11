const VkApi = require('./VkApi');
const Mongo = require('./Mongo');
const Elastic = require('../services/Elastic');

class CommentWorker {

    constructor(config) {
        this.config = config;
        this.api_response = null;

        this.api_error = null;

        this.report = {};

        this.collection = Mongo.getCommentsCollection();
    }

    async run() {
        const api_response = await VkApi.getPostComments(this.config.post_id, this.config.group_config.vk_group_id);

        if (api_response.response && api_response.response.first_level_count >= 0 && api_response.response.items) {
            this.api_response = api_response.response;

            await this.createReport();

            return true;

        } else {
            this.api_error = api_response;
            console.log(this.api_error);
            return false;
        }
    }

    error() {
        return this.api_error;
    } 

    result() {
        return this.api_response;
    }

    async createReport() {

        this.api_response.items.forEach( async (item) => {

            if (item.thread && item.thread.count > 0) {

                const thread_items = [...item.thread.items];

                item.thread.items = [];

                await this.parseComment(item);

                thread_items.forEach( async (i) => {
                    await this.parseComment(i);
                });

            } else {
                await this.parseComment(item);
            }

        });
    }

    async parseComment(comment) {

        const full_id = `${Math.abs(comment.owner_id).toString()}_${comment.id}`;
        comment.full_id = full_id;
        const isExists = await this.collection.find({full_id: full_id}, {_id: 1}).limit(1).count();
        if (isExists > 0) return false;

        this.collection.insertOne(comment);
        const res = await Elastic.indexComment(comment);
    }

}

module.exports = CommentWorker;