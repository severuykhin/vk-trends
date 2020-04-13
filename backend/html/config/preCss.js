"use strict";

const { params, plugins: $ } = require("./variables");

module.exports = () =>
    $.gulp.src(params.sass)
        .pipe($.plumber())
        .pipe($.concat("cache.scss"))
        .pipe($.gulp.dest(params.out));