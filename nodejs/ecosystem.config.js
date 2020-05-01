module.exports = {
    apps: [
        {
            name: 'Parser',
            script: 'index.js',
            instances: 1,
            autorestart: true,
            watch: true,
            max_memory_restart: '1G',
            env: {
                NODE_ENV: 'development'
            },
            env_production: {
                NODE_ENV: 'production'
            }
        },
        {
            name: 'Producer web socket',
            script: 'ProducerWebSocket.js',
            instances: 1,
            autorestart: true,
            watch: true,
            max_memory_restart: '1G',
            env: {
                NODE_ENV: 'development'
            },
            env_production: {
                NODE_ENV: 'production'
            }
        },
        {
            name: 'Rabbit status worker',
            script: 'RabbitStatusWorker.js',
            instances: 1,
            autorestart: true,
            watch: true,
            max_memory_restart: '1G',
            env: {
                NODE_ENV: 'development'
            },
            env_production: {
                NODE_ENV: 'production'
            }
        },
    ]
};
