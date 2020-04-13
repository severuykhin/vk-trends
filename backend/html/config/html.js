"use strict";

const { params, plugins: $ } = require("./variables");

module.exports = function () {
    let dataLocals = {};

    try {
        dataLocals = JSON.parse($.fs.readFileSync("./blocks/data.json", "utf8"));
    } catch (e) {
        console.error(e);
    }

    return $.gulp.src(params.htmlSrc)
        .pipe($.plumber())
        .pipe($.pug({
            locals: dataLocals,
            pretty: true
        }))
        .pipe($.gulp.dest(params.out))
        .pipe($.htmlmin({
            minifyJS: true,
            minifyCSS: true,
            removeComments: true
        }))
        .pipe($.gulp.dest(params.prod));
};