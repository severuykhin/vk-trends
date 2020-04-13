const WS = require('ws');
const express = require('express');

let socket = null;

class PWS {
    listen() {
        socket = new WS.Server({ port: 1234 });

        socket.on('connection', connection => {
            console.log('New connection');
        });

    }
}

const pws = new PWS();

pws.listen();

const app = express();
const port = 8001;

app.use(express.json())
// app.use(express.urlencoded())

app.post('/message', async (request, response) => {
    if (!socket) {
        response.json({'result': 'error', 'message': 'socket is not available'});
    } else {

        if (request.body && request.body.message) {

            try {
                let message = JSON.stringify(request.body.message);
                socket.clients.forEach( client => {
                    client.send(message);
                });
            } catch (e) {
                socket.clients.forEach( client => {
                    client.send(JSON.stringify({type: "error", 'message': 'Some error message'}));
                });
            }
        }

        response.json({'result': 'success', 'message': 'ok'});
    }
})

app.listen(port, (err) => {
    if (err) {
        return console.log('something bad happened', err)
    }
})