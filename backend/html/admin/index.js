import '@babel/polyfill';
import Websocket from './workers/Websocket';
import GroupIndex from './modules/GroupIndex'
import RabbitStatus from './modules/RabbitStatus';

const ws = new Websocket();

const gi = new GroupIndex().init();
const rs = new RabbitStatus();

rs.init();

ws.create({
    subscribers: {
        'rabbit-status': [rs]
    }
});