"use strict";

const { params, plugins: $ } = require("./variables");

module.exports = () =>
    $.gulp.src("bower_components/jquery/dist/jquery.min.js")
        .pipe($.gulp.dest(`${params.out}/js`))
        .pipe($.gulp.dest(`${params.prod}/js`));