"use strict";

const { params, plugins: $ } = require("./variables");

params.images.push(params.type.images);

module.exports = () =>
    $.gulp.src(params.images)
        .pipe($.rename( {dirname: ""} ))
        .pipe($.gulp.dest(`${params.out}/images`))
        .pipe($.gulp.dest(`${params.prod}/images`))
        .pipe($.gulp.dest(`${params.site}/images`))
        .pipe($.reload({stream: true}));