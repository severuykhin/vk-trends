const moment = require('moment');
const time_format_moment = 'YYYY-MM-DD HH:mm:ss.SSSSSS';

module.exports = function (timestamp_vk) {
    let timestamp = '';

    if (!timestamp_vk) {
        return moment.utc(new Date()).format(time_format_moment);
    }

    try {
        timestamp = moment.utc(new Date(timestamp_vk * 1000)).format(time_format_moment);
    } catch (e) {
        timestamp = moment.utc(new Date()).format(time_format_moment);
    }

    return timestamp;
}