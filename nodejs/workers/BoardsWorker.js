const VkApi = require('./VkApi');
const Mongo = require('./Mongo');
const Elastic = require('../services/Elastic');
const TextCleaner = require('../services/TextCleaner');

class BoardsWorker {

    constructor(config) {
        this.config = config;
        this.api_response = null;

        this.api_error = null;

        this.report = {};

        this.collection = Mongo.getBoardsCollection();
        this.tc = new TextCleaner();
    }

    async run() {
        
        const api_response = await VkApi.getBoardComments(this.config.board_id, this.config.group_config.vk_group_id);

        if (api_response.response && api_response.response.items) {
            this.api_response = api_response.response;

            await this.createReport();

            return true;

        } else {
            this.api_error = api_response;
            if (this.api_error.error && this.api_error.error.error_code === 13) {
                return true;
            }

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
            await this.parseComment(item);
        });
    }

    async parseComment(comment) {

        const full_id = `${comment.id}-${this.config.board_id}-${this.config.group_config.vk_group_id}`;
        
        comment.full_id = full_id;
        comment.city = this.config.group_config.city_id.toString();
        comment.categories = this.config.group_config.categories;
        comment.lng = this.config.group_config.lng;
        comment.ltd = this.config.group_config.ltd;
        comment.owner_id = this.config.group_config.vk_group_id;
        comment.report_id = this.config.report_id; 

        const isExists = await this.collection.find({full_id: full_id}, {_id: 1}).limit(1).count();
        
        if (isExists > 0 || false === this.tc.check(comment.text)) return false;   
        
        let insertRes = await this.collection.insertOne(comment);

        if (insertRes.insertedId) {
            comment.mongo_id = insertRes.insertedId;
            Elastic.indexBoardComment(comment)
                .then(data => {
                   // Do smth with response 
                })
        }

        return true;

    }

}

module.exports = BoardsWorker;