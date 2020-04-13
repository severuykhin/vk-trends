class Websocket {

    constructor() {
        this.websocket = null;
    }

    create(config) {
        this.websocket = new WebSocket(`ws://localhost:1234`);
        this.config = config;

        const self = this;

        this.websocket.onopen = function () {
            console.log("Websocket connection ready");
        }

        this.websocket.onclose = function (event) {

            console.log('Websocket connection closed' + (event.wasClean ? ' clean' : ''));
            console.log('Websocket closed with code: ' + event.code + ', reason: ' + event.reason);

            setTimeout(function () {
                self.create(config);
            }, 3000);
        }

        this.websocket.onmessage = function (event) {
            let message;

            try {
                message = JSON.parse(event.data);
            } catch (e) {
                /**
                 * @todo - error handler
                 */
                throw e;
            }

            let type = message.type;

            if (!type) {
                console.warn(`WEBSOCKET: No type ${type}`);
            }

            const consumers = self.config.subscribers[type];

            if (consumers && consumers.length > 0) {
                consumers.forEach(consumer => {
                    if (typeof consumer.consume === 'function') {
                        consumer.consume(message.data);
                    }
                });
            }

        };
    }

}

export default Websocket;