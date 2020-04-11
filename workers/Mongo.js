const MongoClient = require("mongodb").MongoClient;

class MongoConstructor {
    constructor() {
        this.url = 'mongodb://localhost:27017/vkgroups';
        this.client = null;
        this.db = null;
    }

    async init() {

        try {
            const client = await MongoClient.connect(this.url, {useUnifiedTopology: true});
            this.client = client;

        } catch (e) {
            console.log();
        }

    }

    getReportsCollection() {
        if (this.client) {
            const db = this.client.db('vkgroups');
            const reports = db.collection('reports');
            return reports;
        } else {
            return null;
        }
    }

    getCommentsCollection() {
        if (this.client) {
            const db = this.client.db('vkgroups');
            const comments = db.collection('comments');
            return comments;
        } else {
            return null;
        }
    }

    getPostsCollection() {
        if (this.client) {
            const db = this.client.db('vkgroups');
            const posts = db.collection('posts');
            return posts;
        } else {
            return null;
        }
    }
}

const Mongo = new MongoConstructor();

module.exports = Mongo;