"use strict";

const { params, plugins: $ } = require("./variables");

module.exports = () =>
    $.gulp.src(params.json)
        .pipe($.plumber())
        .pipe($.merge("data.json"))
        .pipe($.gulp.dest("./blocks"));