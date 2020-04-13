"use strict";

const { plugins: $ } = require("./variables");

module.exports = () =>
    $.gulp.src("./blocks/data.json", {read: false})
        .pipe($.plumber())
        .pipe($.clean());