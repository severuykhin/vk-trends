"use strict";

const browserSync  = require("browser-sync").create();
const reload       = browserSync.reload;

module.exports = {
    bs     : browserSync,
    reload : reload
};