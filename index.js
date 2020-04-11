const express = require('express')
const Consumer = require('./queue/consumer');
const Producer = require('./queue/producer');
const GroupWorker = require('./workers/GroupWorker');
const CommentWorker = require('./workers/CommentWorker');
const VkApi = require('./workers/VkApi');
const database = require('./services/database');
const MongoDB = require('./workers/Mongo');
const Elastic = require('./services/Elastic');

Elastic.createIndexes();

database.init()
MongoDB.init();

const Group = require('./models/Group');

const app = express()
const port = 8000

const producer = new Producer();
producer.init();

const consumer = new Consumer();
consumer.init();

app.get('/', (request, response) => {
    response.send('vk groups')
})

app.get('/api/process/:group_id', (request, response) => {
    let group_id = request.params.group_id;

    if (!group_id) {
        response.send('Error. No group id');
    }

    // Create new Report

    const groupWorker = new GroupWorker(producer);
    groupWorker.run(group_id);

    response.send('ok');
})

app.get('/api/test/:group_id', async (request, response) => {
    let group_id = request.params.group_id;

    if (!group_id) {
        response.send('Error. No group id');
    }

    const group = new Group();
    const group_data = group.getById(1);

    const g_config = {
        vk_group_id: 60609780
    }

    const comment_worker_data = {
        type: 'comments',
        report_id: 1,
        total: 123,
        index: 1,
        post_id: 3982960,
        group_config: g_config
    }

    const comment_worker = new CommentWorker(comment_worker_data);

    await comment_worker.run();

    if (comment_worker.error()) {
        response.json(comment_worker.error());
    }

    response.json(comment_worker.result());
})

app.listen(port, (err) => {
    if (err) {
        return console.log('something bad happened', err)
    }
})