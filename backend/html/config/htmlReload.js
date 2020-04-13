"use strict";

const { params, plugins: $ } = require("./variables");

module.exports = () =>
    $.gulp.src(params.htmlSrc)
        .pipe($.reload({stream: true}));
