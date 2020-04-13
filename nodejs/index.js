const express = require('express')
const Consumer = require('./queue/consumer');
const Producer = require('./queue/producer');
const GroupWorker = require('./workers/GroupWorker');
const CommentWorker = require('./workers/CommentWorker');
const VkApi = require('./workers/VkApi');
const database = require('./services/database');
const MongoDB = require('./workers/Mongo');
const Elastic = require('./services/Elastic');

(async () => {
    await Elastic.createIndexes()
    await database.init()
    await MongoDB.init()
})()

const app = express()
const port = 8000

const producer = new Producer();
producer.init();

const consumer = new Consumer();
consumer.init();

app.get('/', (request, response) => {
    response.send('vk groups')
})

app.post('/api/process/:group_id', async (request, response) => {
    let group_id = request.params.group_id;

    if (!group_id) {
        response.send('Error. No group id');
    }

    // Create new Report

    const groupWorker = new GroupWorker(producer);
    const res = await groupWorker.run(group_id);
    
    if (res.ops && res.ops[0]) {
        response.json(res.ops[0]);
    } else {
        response.json({ result: 'error' });
    }
})


app.listen(port, (err) => {
    if (err) {
        return console.log('something bad happened', err)
    }
})