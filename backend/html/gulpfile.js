"use strict";

const { plugins: { gulp } } = require("./config/variables");

const createTask = function (taskName, beforeTasks) {
    console.log(taskName);
    const task = require(`./config/${taskName}`)
    console.log(task);
    if ( !beforeTasks ) return gulp.task( taskName, task );

    return gulp.task( taskName, beforeTasks, task );
};

const tasks = [
    [ "server" ],
    [ "json-clean" ],
    [ "json", ["json-clean"] ],
    [ "fonts" ],
    [ "meta"  ],
    [ "html", ["json"] ],
    [ "htmlReload", ["html"] ],
    [ "preCss" ],
    [ "css", ["preCss"] ],
    [ "adminCss" ],
    [ "images" ],
    [ "jsLibs" ],
    [ "js" ],
    [ "jquery" ],
    [ "createFirstLevelBlocks" ],
    [ "createBlocks" ]
];

gulp.task("default", ["server", "build"]);

gulp.task("build", ["html", "fonts", "meta", "images", "js", "jquery"]);

for( let taskName of tasks ) {
    createTask(...taskName);
}