const axios = require('axios');
const config = require('./config');

class RabbitStatusWorker {
    run() {
        setInterval(async () => {
            const res = await axios.get(config.rabbit_api_url, {
                auth: {
                    username: 'guest',
                    password: 'guest'
                }
            });

            let data = res.data;

            if (data) {
                axios.post(config.socket_producer_host, {
                    'message': {
                        type: 'rabbit-status',
                        data: data
                    }
                });
            }

        }, 3000);
    }
}

const worker = new RabbitStatusWorker();

worker.run();

