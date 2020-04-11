const mysql = require("mysql2");


class Database {

    constructor() {
        this.connection = null;
    }

    init() {
        const connection = mysql.createConnection({
            host: "localhost",
            user: "igor",
            database: "vkgroups",
            password: "2205"
        });

        this.connection = connection;

    }

}

module.exports = new Database();