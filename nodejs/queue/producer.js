const amqp = require('amqplib/callback_api');
const axios = require('axios');
const constants = require('../includes/constants');

class Producer {

  constructor(group_id) {
    this.connection = null;
    this.channel = null;
    this.group_id = group_id;
  }

  init() {
    amqp.connect('amqp://localhost', (error0, connection) => {
      if (error0) {
        throw error0;
      }

      this.connection = connection;

      connection.createChannel((error1, channel) => {
        if (error1) {
          throw error1;
        }

        this.channel = channel;

        channel.assertQueue(`comments`, { durable: true, noAck: false });
        channel.assertQueue(`posts`, { durable: true, noAck: false });

      })
    })
  }

  send(message) {

    if (!this.channel) {
      throw new Error('No channel');
    }

    let result = false;

    if (message.type === 'comments') {
      result = this.channel.sendToQueue(`comments`, Buffer.from(JSON.stringify(message)));
    }
    if (message.type === 'posts') {
      result = this.channel.sendToQueue(`posts`, Buffer.from(JSON.stringify(message)));
    }

    return result;
  }

}

module.exports = Producer;

