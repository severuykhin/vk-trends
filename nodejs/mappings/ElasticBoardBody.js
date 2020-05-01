const getTimestamp = require('../services/getTimeStamp');
const TextCleaner = require('../services/TextCleaner');

module.exports = function (comment) {

    const tc = new TextCleaner();

    return {
        "mongo_id": comment.mongo_id,
        "report_id": comment.report_id ? comment.report_id : '',
        "vk_id": comment.id,
        "from_id": comment.from_id,
        "owner_id": comment.owner_id,
        "full_id": comment.full_id,
        "@timestamp": getTimestamp(comment.date),
        "index_time": getTimestamp(),
        "keys": tc.clean(comment.text),
        "text": comment.text,
        "length": comment.text ? comment.text.length : 0,
        "categories": comment.categories && comment.categories.length > 0 ? comment.categories : [],
        "city": comment.city ? comment.city : 0,
        "lng": comment.lng,
        "ltd": comment.ltd
    };
}