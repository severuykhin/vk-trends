const mysql = require("mysql2");


class Database {

    constructor() {
        this.connection = null;
    }

    init() {
        const connection = mysql.createConnection({
            host: "localhost",
            user: "root",
            database: "vkgroups",
            password: "123"
        });

        this.connection = connection;

    }

}

module.exports = new Database();