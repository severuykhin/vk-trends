"use strict";

const { params, plugins: { fs } } = require("./variables");

let folder, file, fileJade, fileJSON;

module.exports = function () {
    for ( let blockName of params.blocksName ) {
        folder     = `./blocks/${blockName}`; // ./blocks/example
        file       = `${folder}/${blockName}`;
        fileJade   = `${file}.pug`;
        fileJSON   = `${file}.json`;

        try {
            fs.mkdirSync(folder);
            fs.writeFileSync(fileJade, `.${blockName}`);
            fs.writeFileSync(fileJSON, `{\n  "${blockName}" : {\n\n  }\n}`);
        } catch (err) {
            console.log(`${blockName} already exists!`);
        }

    }
};