# imagemin-pngquant

* 

## Установка

* Устанавливаем imagemin-pngquant локально с помощью ключа --save

> $ npm install --save imagemin-pngquant

* Подключаем переменные

```js
var pngquant = require('imagemin-pngquant')
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