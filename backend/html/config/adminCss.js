"use strict";

const { params, plugins: $ } = require("./variables");

const processors = [
    $.autoprefixer({browsers: ["last 4 version"]}),
    $.csso
];

module.exports = () =>
    $.gulp.src("admin/styles.scss")
        .pipe($.plumber())
        .pipe($.sass.sync())
        .pipe($.postcss(processors))
        .pipe($.rename("adminStyles.css"))
        .pipe($.gulp.dest(params.out))
        .pipe($.gulp.dest(params.prod))
        .pipe($.gulp.dest(params.site))
        .pipe($.reload({ stream: true }));