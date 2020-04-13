"use strict";

const { bs, reload } = require("../browserSync");

module.exports = {
    params : {
        out: "public",
        prod: "public/prod",
        site: "../web/statics",
        htmlSrc: "pug/*.pug",
        levels: ["xs", "sm", "md", "lg", "xl"],
        html: ["pug/*.pug", "blocks/**/*.pug"],
        blocksName : [
            "select"
        ],
        js: [],
        jsLibs : [
            "node_modules/jquery/dist/jquery.min.js",
            "vendor/yiisoft/yii2/assets/yii.js",
            "vendor/yiisoft/yii2/assets/yii.validation.js",
            "vendor/yiisoft/yii2/assets/yii.activeForm.js"            
        ],
        json: "blocks/**/*.json",
        css: [],
        sass: [
            "setting.block/bootstrap.scss",
            "setting.block/custom.scss",
        ],
        images: [
        ],
        type: {
            css     : "blocks/**/**/*.css",
            sass    : "blocks/**/**/*.scss",
            js      : "blocks/**/**/*.{js,ts}",
            adminJs : "admin/**/**/*.js",
            images  : "blocks/**/**/*.{gif,jpg,png,ico,svg}",
            json    : "blocks/**/*.json" 
        },
        fonts : 'fonts/*',
        meta  : 'meta/*'
    },
    plugins: {
        gulp          : require("gulp"),
        concat        : require("gulp-concat"),
        rename        : require("gulp-rename"),
        path          : require("path"),
        url           : require("gulp-css-url-adjuster"),
        autoprefixer  : require("autoprefixer"),
        postcss       : require("gulp-postcss"),
        pug           : require("gulp-pug"),
        babel         : require("gulp-babel"),
        jshint        : require("gulp-jshint"),
        plumber       : require("gulp-plumber"),
        uglify        : require("gulp-uglify"),
        sass          : require("gulp-sass"),
        fs            : require("fs"),
        clean         : require("gulp-clean"),
        replace       : require("gulp-replace"),
        merge         : require("gulp-merge-json"),
        htmlmin       : require("gulp-htmlmin"),
        gcmq          : require('gulp-group-css-media-queries'),
        csso          : require("postcss-csso"),
        bs            : bs,
        reload        : reload,
        webpack       : require("webpack"),
        webpackConfig : require("../../webpack.config"),
        webpackGulp   : require("webpack-stream")
    }
};