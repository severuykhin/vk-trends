"use strict";

const { params, plugins: $ } = require("./variables");

module.exports = () => {
    return $.gulp.src(params.meta)
        .pipe($.gulp.dest(params.out))
        .pipe($.gulp.dest(params.prod))
        .pipe($.gulp.dest(params.site))
}