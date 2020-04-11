fs = require('fs');

const GROUP_ID = process.argv[2];
const PORT = process.argv[3];

const express = require('express')
const Consumer = require('./queue/consumer');
const Producer = require('./queue/producer');
const GroupWorker = require('./workers/GroupWorker');
const CommentWorker = require('./workers/CommentWorker');
const VkApi = require('./workers/VkApi');
const database = require('./services/database');
const MongoDB = require('./workers/Mongo');

database.init()
MongoDB.init();

const Group = require('./models/Group');

const app = express();

const producer = new Producer(GROUP_ID);
producer.init();

const consumer = new Consumer(GROUP_ID);
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
        vk_group_id: 60609780,
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

    response.json(comment_worker);
})

app.listen(PORT, (err) => {
    if (err) {
        return console.log('something bad happened', err)
    }
})

// fs.writeFileSync('log.txt', GROUP_ID);
fs.writeFileSync('log.txt', `Stared daemon for ${GROUP_ID} on port ${PORT}`);