const Database = require('../services/database');

class Group {

    constructor() {
        this.db = Database;
        this.table_name = 'vk_groups';
    }

    getById(id) {
        
    }
}

module.exports = Group;