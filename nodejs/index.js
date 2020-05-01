const express = require('express')
const cluster = require("cluster")
const totalCPUs = require('os').cpus().length;
const Consumer = require('./queue/consumer');
const Producer = require('./queue/producer');
const GroupWorker = require('./workers/GroupWorker');
const CommentWorker = require('./workers/CommentWorker');
const VkApi = require('./workers/VkApi');
const database = require('./services/database');
const MongoDB = require('./workers/Mongo');
const Elastic = require('./services/Elastic');

if (cluster.isMaster) {
    
    console.log(`Total Number of CPU Counts is ${totalCPUs}`);

    for (var i = 0; i < totalCPUs; i++) {
        cluster.fork();
    }
    cluster.on("online", worker => {
        console.log(`Worker Id is ${worker.id} and PID is ${worker.process.pid}`);
    });
    cluster.on("exit", worker => {
        console.log(`Worker Id ${worker.id} and PID is ${worker.process.pid} is offline`);
        console.log("Let's fork new worker!");
        cluster.fork();
    });

} else {

    let producer, consumer;

(async () => {
    try {
        await Elastic.createIndexes()
        await database.init()
        await MongoDB.init()

        producer = new Producer();
        producer.init();

        consumer = new Consumer();
        consumer.init();

    } catch (e) {
        console.log(e)
        process.exit(1)
    }
})()

const app = express()
const port = 8000

app.get('/', (request, response) => {
    response.send('vk groups')
})

app.post('/api/process/:group_id', async (request, response) => {

    try {
        let group_id = request.params.group_id;

        if (!group_id) {
            response.send('Error. No group id');
        }

        // Create new Report

        const groupWorker = new GroupWorker(producer);

        await groupWorker.load(group_id);

        const res = await groupWorker.run();
        const res_boards = await groupWorker.runBoards();

        const resp = {
            'result': 'success'
        };

        if (res.ops && res.ops[0]) {
            resp.comments = res.ops[0];
        } else {
            resp.comments = 'error';
        }

        if (res_boards.ops && res_boards.ops[0]) {
            resp.boards = res_boards.ops[0];
        } else {
            resp.boards = error;
        }

        response.json(resp);

    } catch (e) {
        response.json({
            'result': 'error',
            'message': e.message
        });
    }
})

// app.get('/api/process/test', async (request, response) => {
//     const gw = new GroupWorker(producer);

//     await gw.load(124087268);

//     const res = await gw.runBoards();

//     response.json(res);
// });


app.listen(port, (err) => {
    if (err) {
        return console.log('something bad happened', err)
    }
})

}
