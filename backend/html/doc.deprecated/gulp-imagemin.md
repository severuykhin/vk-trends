# gulp-imagemin

* Сжимает избражения типа PNG, JPEG, GIF и SVG. Все остальные (не поддерживаемые) форматы графических файлов просто игнорируются плагином gulp-imagemin

## Установка

* Устанавливаем gulp-imagemin локально с помощью ключа --save-dev

> $ npm install --save-dev gulp-imagemin

* Подключаем переменную

```js
var imagemin = require('gulp-imagemin');
```

* создаем задачу и прописываем в нее следующее

```js
gulp.task('default', function () {
    return gulp.src('/images/*') - берет любые изображения из папки images
        .pipe(imagemin({
            progressive: true, - Эта опция управляет методом сжатия графических файлов          
        }))
        .pipe(gulp.dest('dist/images')); - заменяет старые, не сжатые картинки, на новые, меньшего размера
});
```