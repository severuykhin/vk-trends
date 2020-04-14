const mysql = require("promise-mysql");


class Database {

    constructor() {
        this.connection = null;
    }

    async init() {
        const connection = await mysql.createConnection({
            host: "localhost",
            user: "igor",
            database: "vkgroups",
            password: "2205"
        });

        this.connection = connection;

    }

    async getGroupData(id) {
        try {
            const res = await this.connection.query('SELECT * FROM `group` WHERE `vk_group_id` = ' + id);
            return res;
        } catch (e) {
            return [];
        }
    }

    async getGroupToCategoryData(id) {

        try {
            const res = await this.connection.query('SELECT * FROM `group_to_category` WHERE `group_id` = ' + id);
            return res;
        } catch (e) {
            return [];
        }
    
    }

    async getCity(id) {

        try {
            const res = await this.connection.query('SELECT * FROM `city` WHERE `id` = ' + id);
            if (res && res.length > 0) {
                return res[0];
            } else {
                return null;
            }
        } catch (e) {
            return null;
        }
    
    }

}

module.exports = new Database();