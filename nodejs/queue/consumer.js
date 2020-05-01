const amqp = require('amqplib/callback_api');
const CommentWorker = require('../workers/CommentWorker');
const LikesWorker = require('../workers/LikesWorker');
const PostsWorker = require('../workers/PostsWorker');
const BoardsWorker = require('../workers/BoardsWorker');
const Mongo = require('../workers/Mongo');

class Consumer {

    constructor(group_id) {
        this.queues = [];
        this.group_id = group_id;
    }

    init() {
        amqp.connect('amqp://localhost', (error0, connection) => {
            if (error0) {
                throw error0;
            }

            connection.createChannel((error1, channel) => {

                if (error1) {
                    throw error1;
                }

                channel.prefetch(1);

                channel.assertQueue(`comments`, {durable: true, noAck: false}, (err, ok) => {
                    if (err) {
                        console.log(err);
                    }

                    channel.consume(`comments`, async (msg) => {
                    
                        try {
                            const mess = JSON.parse(msg.content.toString());
                            const res = await this.processMessage(mess, `comments`);

                            const col = Mongo.getReportsCollection();
                            const reports = await col.find({ report_id: mess.report_id }).limit(1).toArray();


                            if (res) {

                                if (reports && reports[0]) {
                                    let successCount = reports[0].success + 1;
                                    if (successCount === reports[0].total) {
                                        await col.updateOne({ 
                                            report_id: mess.report_id 
                                        }, {
                                            $set: {
                                                success: successCount, 
                                                status: 1,
                                                time_end: new Date()
                                            }
                                        });
                                    } else {
                                        await col.updateOne({ 
                                            report_id: mess.report_id 
                                        }, {
                                            $set: {success: successCount}
                                        });
                                    }
                                }

                                setTimeout(async () => {
                                    channel.ack(msg);
                                }, 100);
                            } else {
                                if (reports && reports[0]) {
                                    let errorsCount = reports[0].errors + 1;
                                    await col.updateOne({ 
                                        report_id: mess.report_id 
                                    }, {
                                        $set: {errors: errorsCount}
                                    });
                                    setTimeout(() => {
                                        channel.reject(msg);
                                    }, 50);
                                }
                            }
                        } catch (e) {
                            console.log(e);
                        }

                    }, {noAck: false})

                });

                channel.assertQueue(`posts`, { durable: true, noAck: false }, (err, ok) => {
                    if (err) {
                        console.log(err);
                    }

                    channel.consume('posts', async (msg) => {

                        try {
                            const mess = JSON.parse(msg.content.toString());
                            const res = await this.processMessage(mess, `posts`);

                            if (res) {
                                channel.ack(msg);
                            } else {
                                console.log(false, 'post');
                                channel.reject(msg);
                            }
                        } catch (e) {
                            console.log(e);
                            channel.reject(msg);
                        }

                    }, { noAck: false });
                });

                channel.assertQueue(`boards`, { durable: true, noAck: false }, (err, ok) => {
                    if (err) {
                        console.log(err);
                    }

                    channel.consume('boards', async (msg) => {

                        try {
                            const mess = JSON.parse(msg.content.toString());
                            const res = await this.processMessage(mess, `boards`);

                            if (res) {
                                channel.ack(msg);
                            } else {
                                console.log(false, 'boards');
                                channel.reject(msg);
                            }
                        } catch (e) {
                            console.log(e);
                            channel.reject(msg);
                        }

                    }, { noAck: false });
                });

            })
        })
    }

    async processMessage(message, queue) {
        try {
            const data = message;

            let worker = null;
            let res = true;

            switch (data.type) {
                case 'comments':
                    worker = new CommentWorker(data);
                    res = await worker.run();
                    return res;
                case 'posts':
                    worker = new PostsWorker(data);
                    res = await worker.run();
                    return res;
                case 'boards':
                    worker = new BoardsWorker(data);
                    res = await worker.run();
                    return res;
                case 'likes':
                    worker = new LikesWorker(data);
                    res = await worker.run();
                    return res;
                default:
                    this.logUnrecognizedMessageTypeError(message, queue);
                    return res;
            }

        } catch (e) {
            console.log(e);
            this.logMessageJsonError(message, queue);
            return true;
        }
    }

    logMessageJsonError(message, queue) {

    }

    logUnrecognizedMessageTypeError(message, queue) {

    }
}

module.exports = Consumer;