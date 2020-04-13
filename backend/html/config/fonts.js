"use strict";

const { params, plugins: $ } = require("./variables");

module.exports = () => {
    return $.gulp.src(params.fonts)
        .pipe($.gulp.dest(`${params.out}/fonts`))
        .pipe($.gulp.dest(`${params.prod}/fonts`))
}
